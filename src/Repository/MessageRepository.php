<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
    {
    public function __construct(ManagerRegistry $registry)
        {
        parent::__construct($registry, Message::class);
        }
    public function getRecentMessagesObj(int $limit = 20): array
        {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT chat_messages.id, chat_messages.message, chat_messages.message_time, users.login
            FROM chat_messages
            JOIN users ON chat_messages.user_id = users.id
            ORDER BY chat_messages.message_time DESC
            LIMIT :limit
        ';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
        }


    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    }
