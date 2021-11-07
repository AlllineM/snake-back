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
        $this->updateStatus($currentUserId, $userId);

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
     * Делает прочитанными все сообщения от userId (меняет статус с NEW на READ)
     * @param $currentUserId
     * @param $userId
     */
    public function updateStatus($currentUserId, $userId)
    {
        $unread = $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.userFrom = :userId AND m.userTo = :currentUser')
            ->andWhere('m.status = :status')
            ->setParameters([
                'currentUser' => $currentUserId,
                'userId' => $userId,
                'status' => Message::NEW,
            ])
            ->getQuery()
            ->getResult();

        foreach ($unread as $message) {
            $message->setStatus(Message::READ);
            $entityManager = $this->getEntityManager();
            $entityManager->persist($message);
            $entityManager->flush();
        }
    }

    /**
     * Возвращает общее число непрочитанных сообщений
     * @param $currentUserId
     * @return mixed
     */
    public function getUnreadGeneral($currentUserId)
    {
         return $this->createQueryBuilder('m')
            ->select('count(m.id) as cnt')
            ->where('m.userTo = :currentUser')
            ->andWhere('m.status = :status')
            ->setParameters([
                'currentUser' => $currentUserId,
                'status' => Message::NEW,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * Возвращает количество непрочитанных сообщений от каждого юзера отдельно
     * @param $currentUserId
     * @return mixed
     */
    public function getUnread($currentUserId)
    {
         $rows = $this->createQueryBuilder('m')
            ->select(['count(m.id) as cnt', 'u.id as userFrom'])
            ->leftJoin('m.userFrom','u')
            ->where('m.userTo = :currentUser')
            ->andWhere('m.status = :status')
            ->setParameters([
                'currentUser' => $currentUserId,
                'status' => Message::NEW,
            ])
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['userFrom']] = $rows['cnt'];
        }

        return $result;
    }

}
