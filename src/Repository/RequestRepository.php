<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Request|null find($id, $lockMode = null, $lockVersion = null)
 * @method Request|null findOneBy(array $criteria, array $orderBy = null)
 * @method Request[]    findAll()
 * @method Request[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

    /**
     * Выгружает все принятые входящие или исходящие заявки. То бишь друзей
     * @param $userId
     * @return array
     */
    public function getAccepted($userId)
    {
        $ins = $this->getAllHelper($userId, Request::ACCEPTED, 'userTo', 'mate');
        $outs = $this->getAllHelper($userId, Request::ACCEPTED, 'userFrom', 'mate');

        return array_merge($ins, $outs);
    }

    /**
     * Возвращает массив айдишников друзей (юзеров, с принятой заявкой)
     * @param $userId
     * @return array
     */
    public function getFriends($userId)
    {
        $friends = [];
        $accepted = $this->getAccepted($userId);

        foreach ($accepted as $item) {
            $friends[] = $item['mate'];
        }

        return $friends;
    }

    /**
     * Выгружает все активные входящие заявки юзера
     * @param $userId
     * @return array|int|string
     */
    public function getIns($userId)
    {
        return $this->getAllHelper($userId, Request::WAITING, 'userTo');
    }

    /**
     * Выгружает все активные исходящие заявки юзера
     * @param $userId
     * @return array|int|string
     */
    public function getOuts($userId)
    {
        return $this->getAllHelper($userId, Request::WAITING, 'userFrom');
    }

    /**
     * Выгружает входящие/исходящие заявки
     * @param $userId int текущий юзер
     * @param $status int статус заявки
     * @param $userType string Тип текущего юзера. userFrom - для исходящих заявок, userTo - для входящих
     * @param $alias string Для выгрузки друзей
     * @return array|int|string
     */
    private function getAllHelper($userId, $status, $userType, $alias = '')
    {
        $mateType = $userType == 'userFrom' ? 'userTo' : 'userFrom'; //mate - юзер, от кого/кому заявка
        $aliasUserType = 'r.' . $userType;
        $aliasMateType = 'r.' . $mateType;
        $alias = empty($alias) ? $mateType : $alias;

        return $this->createQueryBuilder('r')
            ->select(
                'IDENTITY(' . $aliasMateType . ') as ' . $alias,
                'u.nick')
            ->innerJoin($aliasMateType,'u')
            ->where($aliasUserType .' = :userId')
            ->andWhere('r.status = :status')
            ->setParameters(['userId' => $userId, 'status' => $status])
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }


    // /**
    //  * @return Request[] Returns an array of Request objects
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
    public function findOneBySomeField($value): ?Request
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
