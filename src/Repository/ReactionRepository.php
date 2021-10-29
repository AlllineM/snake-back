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
            ->andWhere('r.status = 1')
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
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
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
