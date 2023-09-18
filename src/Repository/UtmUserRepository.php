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

    /**
     * @param $temporaryId
     * @return float|int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByTemporaryId($temporaryId)
    {
        return $this->createQueryBuilder('utm_user')
            ->andWhere('utm_user.temporaryId = :temporaryId')
            ->setParameter('temporaryId', $temporaryId)
            ->orderBy('utm_user.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
