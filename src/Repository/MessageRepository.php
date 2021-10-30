<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Fact;
use App\Entity\Reaction;
use App\Entity\UserAliker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Fact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fact[]    findAll()
 * @method Fact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Возвращает переписку авторизованного юзера с userId
     * @param $currentUserId
     * @param $userId
     * @return mixed
     */
    public function getMessages($currentUserId, $userId)
    {
        return $this->createQueryBuilder('m')
            ->select(['m.body', 'm.datetime', 'u.id as userTo'])
            ->leftJoin('m.userTo','u')
            ->where('(m.userFrom = :currentUser AND m.userTo = :userId) OR (m.userFrom = :userId AND m.userTo = :currentUser)')
            ->setParameters([
                'currentUser' => $currentUserId,
                'userId'=> $userId,
            ])
            ->orderBy('m.datetime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Возвращает рандомный факт
     * @return mixed
     * @throws \Exception
     */
    public function findRandom($user)
    {
        //выгружаем все оценки
        $likes = array_column(
            $this->getEntityManager()
                ->getRepository(Reaction::class)
                ->findAllReactions($user),
            1);
        $likes[] = 0; //нужно для юзеров, у которых еще нет лайков совсем

        //выгружаем все факты без оценок
        $newFacts = $this->createQueryBuilder('o')
            ->select('o.id')
            ->where('o.id NOT IN(:val)')
            ->setParameter('val', $likes)
            ->getQuery()
            ->getArrayResult();

        //выбираем рандомный факт без оценки
        $randomFact = array_rand($newFacts);
        $randomFact = $newFacts[$randomFact]['id'];

        return $this->createQueryBuilder('f')
            ->select(['f.id', 'f.body'])
            ->where('f.id = :val')
            ->setParameters(['val' => $randomFact])
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];
    }

    // /**
    //  * @return Fact[] Returns an array of Fact objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fact
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
