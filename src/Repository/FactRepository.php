<?php

namespace App\Repository;

use App\Entity\Fact;
use App\Entity\Reaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Fact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fact[]    findAll()
 * @method Fact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fact::class);
    }

    /**
     * Возвращает рандомный факт
     * @return mixed
     * @throws \Exception
     */
    public function findRandom($userId)
    {
        //выгружаем все оценки
        $likes = array_column(
            $this->getEntityManager()
                ->getRepository(Reaction::class)
                ->findAllReactions($userId),
            1);
        $likes[] = 0; //нужно для юзеров, у которых еще нет лайков совсем

        //выгружаем все факты без оценок
        $newFacts = $this->createQueryBuilder('new_facts')
            ->select('new_facts.id')
            ->where('new_facts.id NOT IN(:likes)')
            ->andWhere('new_facts.author <> :user_id')
            ->setParameters([
                'likes' => $likes,
                'user_id' => $userId,
            ])
            ->getQuery()
            ->getArrayResult();

        if (empty($newFacts)) return null;

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

    /**
     * Возвращает все факты определенного автора
     * @param $author
     *
     * @return float|int|mixed|string
     */
    public function getAllByAuthor($author)
    {
        return $this->createQueryBuilder('f')
            ->select(['f.id', 'f.body'])
            ->andWhere('f.author = :val')
            ->setParameter('val', $author)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
