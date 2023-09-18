<?php

namespace App\Repository;

use App\Entity\UtmUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UtmUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method UtmUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method UtmUser[]    findAll()
 * @method UtmUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtmUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UtmUser::class);
    }

    // /**
    //  * @return UtmUser[] Returns an array of UtmUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UtmUser
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
