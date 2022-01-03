<?php

namespace App\Repository;

use App\Entity\Fact;
use App\Entity\Reaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Reaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reaction[]    findAll()
 * @method Reaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reaction::class);
    }

    /**
     * По алиасу возвращает числовое значение статуса
     * @param $statusAlias
     * @return mixed
     */
    public static function getStatus($statusAlias)
    {
        return constant(Reaction::class . '::' . $statusAlias);
    }

    /**
     * Выгружает факты для карточки мэйта
     * @param $userId
     * @param $alikerId
     * @return array|int|string
     */
    public function getMatchDetail($currentUserId, $alikerId)
    {
        $result = [];
        $result['mine'] = $this->getPersonalLikes($currentUserId, $alikerId);
        $result['yours'] = $this->getPersonalLikes($alikerId, $currentUserId);
        $result['common'] = $this->getCommonReactions($currentUserId, $alikerId);

        return $result;
    }

    /**
     * Выгружает лайки юзера
     * @param $userId
     * @param string $alias
     * @return array
     */
    public function findLikes($userId, $alias = 'r')
    {
        return $this->createQueryBuilder($alias)
            ->select('IDENTITY(r.fact)')
            ->where('r.userAliker = :val')
            ->andWhere('r.status = ' . Reaction::LIKE)
            ->setParameter('val', $userId)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Выгружает айдишники фактов, которые юзер уже оценил
     * @param $userId
     * @param string $alias
     * @return array
     */
    public function findAllReactions($userId, $alias = 'r')
    {
        return $this->createQueryBuilder($alias)
            ->select('IDENTITY(r.fact)')
            ->where('r.userAliker = :val')
            ->setParameter('val', $userId)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }


    //todo: это тоже переписать, чтоб один запрос в базу был
    public function getCommonLikes($userOne, $userTwo)
    {
        $likesOne = array_column($this->findLikes($userOne), 1);
        $likesTwo = array_column($this->findLikes($userTwo), 1);
        $likes = array_intersect($likesOne, $likesTwo);

        return $this->getEntityManager()
            ->getRepository(Fact::class)
            ->createQueryBuilder('f')
            ->select('f.id, f.body')
            ->where('f.id IN(:likes)')
            ->setParameter('likes', $likes)
            ->getQuery()
            ->getResult();
    }

    //todo: Посмотреть, можно ли слить это в один запрос. И имеет ли оно смысл
    //todo: Но пока так. пофиг))

    /**
     * Выгружает айдишники мэйтов юзера)) И количество совпадающих лайков
     * @param $likes
     * @param $userId
     * @return mixed
     */
    public function getMates($likes, $userId) {
        return $this->createQueryBuilder('t')
            ->select(['IDENTITY(t.userAliker) as userAliker', 'count(t.id) as cnt', 'u.nick'])
            ->innerJoin('t.userAliker','u')
            ->where('t.status = 1')
            ->andWhere('t.userAliker <> :user')
            ->andWhere('t.fact IN(:likes)')
            ->andWhere('t.fact IN(:likes)')
            ->setParameters(['likes'=> $likes, 'user' => $userId])
            ->groupBy('t.userAliker', 'u.nick')
            ->orderBy('cnt', 'DESC')
           // ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    /**
     * Выгружает факты, которые написаны authorId и понравились userId (like либо same)
     * @param $authorId
     * @param $userId
     * @return array|int|string
     */
    public function getPersonalLikes($authorId, $userId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select fact.body, reaction.status
                from fact
                inner join reaction on reaction.fact_id = fact.id
            where fact.author_id = :authorId
                and reaction.user_aliker_id = :userId
                and (reaction.status = ' . Reaction::LIKE . ' or reaction.status = ' . Reaction::SAME . ')
            ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'authorId' => $authorId,
            'userId' => $userId,
        ]);

        return $stmt->fetchAll();
    }

    public function getCommonReactions($userOneId, $userTwoId)
    {
        $positive = '(' . Reaction::LIKE . ', ' . Reaction::SAME . ')';
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            with r_one AS 
                (SELECT fact_id, status
                FROM reaction
                WHERE user_aliker_id = :userOneId 
                    AND status <> 0),
            with r_two AS 
                (SELECT fact_id, status
                FROM reaction
                WHERE user_aliker_id = :userTwoId
                AND status <> 0)
            select fact.body, reaction.status
                from r_one
                inner join r_two on r_one.fact_id = r_two.fact_id
                inner join fact on r_one.fact_id = fact.id
                where (r_one.status = ' . Reaction::DISLIKE . ' and r_two.status = ' . Reaction::DISLIKE . ')
                    or (r_one.status in ' . $positive . ' and r_two.status in ' . $positive . ')
            ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'userOneId' => $userOneId,
            'userTwoId' => $userTwoId,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Основной алгоритм) Ага. //todo: Разобрать в нормальный вид
     * @param $currentUser
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getNewMates($currentUser)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            with cur_r AS 
                (SELECT fact_id, status
                FROM reaction
                WHERE user_aliker_id = :userId),
            author as (
                select
                    4 as user_a,
                    cur_r.status as cur_us,
                    fact.author_id as user_aliker_id,
                    count(1) as cnt
                from fact
                INNER JOIN cur_r
                    ON cur_r.fact_id = fact.id
                group by fact.author_id, cur_r.status
            ),
            common_r as (    
                SELECT reaction.status as user_a,
                    cur_r.status as cur_us,
                    reaction.user_aliker_id,
                    count(1) as cnt
                FROM reaction
                INNER JOIN cur_r
                    ON reaction.fact_id = cur_r.fact_id
                WHERE reaction.user_aliker_id <> :userId
                group by reaction.status, cur_r.status, reaction.user_aliker_id
                ), 
            common as (
                select * 
                from common_r
                union select * 
                from author
            ),
            wish as (
                select 
                    common.user_aliker_id, 
                    sum(common.cnt * weight.weight) as rating
                from common
                left JOIN weight
                    ON common.cur_us = weight.reaction_one
                        and common.cur_us = weight.reaction_two
                group by common.user_aliker_id
                ),
            result as (
                select 
                    common.user_aliker_id, 
                    sum(common.cnt * weight.weight) as rating
                from common
                left JOIN weight
                    ON common.cur_us = weight.reaction_one
                        and common.user_a = weight.reaction_two
                group by common.user_aliker_id
                )
            select 
                result.user_aliker_id, 
                result.rating as rating,
                wish.rating as wish,
                CEILING((result.rating / wish.rating) * 100) as percent
            from result
            INNER JOIN wish
                ON result.user_aliker_id = wish.user_aliker_id
            WHERE result.rating > 0
            order by rating desc
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['userId' => $currentUser]);

        return $stmt->fetchAll();
    }

    // /**
    //  * @return Reaction[] Returns an array of Reaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reaction
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
