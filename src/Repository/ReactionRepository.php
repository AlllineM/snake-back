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
     * Ищет реакцию
     * @param $userId
     * @param $factId
     * @return float|int|mixed|string
     */
    public function findReaction($userId, $factId)
    {
        return $this->createQueryBuilder('reaction')
            ->where('reaction.userAliker = :userId')
            ->andWhere('reaction.fact = :factId')
            ->setParameters([
                'userId' => $userId,
                'factId' => $factId,
            ])
            ->getQuery()
            ->getResult();
    }

    public function findAllReactions($userId)
    {
        return $this->createQueryBuilder('r')
            ->select(['f.body', 'r.status'])
            ->leftJoin('r.fact','f')
            ->where('r.userAliker = :user_id')
            ->setParameter('user_id', $userId)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Выгружает айдишники фактов, которые юзер уже оценил
     * @param $userId
     * @param string $alias
     * @return array
     */
    public function findAllReactionsIds($userId, $alias = 'r')
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
            ->where('t.status = :status')
            ->andWhere('t.userAliker <> :user')
            ->andWhere('t.fact IN(:likes)')
            ->andWhere('t.fact IN(:likes)')
            ->setParameters([
                'status' => Reaction::LIKE,
                'likes'=> $likes,
                'user' => $userId
            ])
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
                and (reaction.status = :status_like or reaction.status = :status_same)
            ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'status_like' => Reaction::LIKE,
            'status_same' => Reaction::SAME,
            'authorId' => $authorId,
            'userId' => $userId,
        ]);

        return $stmt->fetchAll();
    }

    public function getCommonReactions($currentUserId, $alikerId)
    {
        $positive = '(:status_like, :status_same)';
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            with r_curr AS 
                (SELECT fact_id, status
                FROM reaction
                WHERE user_aliker_id = :currentUserId),
            r_aliker AS 
                (SELECT fact_id, status
                FROM reaction
                WHERE user_aliker_id = :alikerId)
            select 
                fact.body, 
                r_curr.status as current_status, 
                r_aliker.status as aliker_status
            from r_curr
            inner join r_aliker on r_curr.fact_id = r_aliker.fact_id
            inner join fact on r_curr.fact_id = fact.id
            where (r_curr.status = :status_dislike and r_aliker.status = :status_dislike)
                or (r_curr.status in ' . $positive . ' and r_aliker.status in ' . $positive . ')
            ';

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            'status_like' => Reaction::LIKE,
            'status_same' => Reaction::SAME,
            'status_dislike' => Reaction::DISLIKE,
            'currentUserId' => $currentUserId,
            'alikerId' => $alikerId,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Основной алгоритм)
     * @param $currentUser
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getNewMates($currentUser)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            with cur_r AS 
                (SELECT fact_id, status
                FROM reaction
                WHERE user_aliker_id = :userId),
            author as (
                select
                    '" . Reaction::AUTHOR . "'::reaction_status as user_a,
                    cur_r.status as cur_us,
                    fact.author_id as user_aliker_id,
                    count(1) as cnt
                from fact
                INNER JOIN cur_r
                    ON cur_r.fact_id = fact.id
                WHERE fact.author_id <> 1
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
                    AND reaction.user_aliker_id <> 1
                group by reaction.status, cur_r.status, reaction.user_aliker_id
                ), 
            common as (
                select * 
                from common_r
                union select * 
                from author
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
                user_aliker.nick,
                result.rating as rating
            from result
            left join user_aliker on result.user_aliker_id = user_aliker.id
            WHERE result.rating > 0
            order by rating desc
            ";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['userId' => $currentUser]);

        return $stmt->fetchAll();
    }
}
