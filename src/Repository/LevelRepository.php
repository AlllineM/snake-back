<?php

namespace App\Repository;

use App\Entity\Level;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Level|null find($id, $lockMode = null, $lockVersion = null)
 * @method Level|null findOneBy(array $criteria, array $orderBy = null)
 * @method Level[]    findAll()
 * @method Level[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Level::class);
    }

    /**
     * Возвращает все базовые уровни
     *
     * @return float|int|mixed|string
     */
    public function getAllBase()
    {
        return $this->createQueryBuilder('l')
            ->select(['l.id', 'l.blocks'])
            ->andWhere('l.user_token = :user_token')
            ->setParameter('user_token', '3')
            ->getQuery()
            ->getResult();
    }

    /**
     * Возвращает все кастомные уровни
     *
     * @return float|int|mixed|string
     */
    public function getAllCustom()
    {
        return $this->createQueryBuilder('l')
            ->select(['l.id', 'l.blocks'])
            ->andWhere('l.user_token <> :user_token')
            ->setParameter('user_token', '3')
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
