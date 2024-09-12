<?php

namespace App\Repository;

use App\Entity\Favorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @extends ServiceEntityRepository<Favorite>
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,)
    {
        parent::__construct($registry, Favorite::class);
    }


    public function isFavorite(int $userId, int $postId): ?Favorite
    {
        return $this->findOneBy([
            'user' => $userId,
            'post' => $postId
        ]);
    }
    public function getEmailsByPostId(int $postId): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT u.email 
        FROM favorite_posts f
        INNER JOIN users u ON f.user_id = u.id
        WHERE f.post_id = :postId';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('postId', $postId);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }


    //    /**
    //     * @return Favorite[] Returns an array of Favorite objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Favorite
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
