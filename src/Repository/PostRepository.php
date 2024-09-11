<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    public function save(Post $post, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($post);
        if ($flush) {
            $entityManager->flush();
        }
    }
    public function findAllByView(int $limit, int $offset)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.view_count', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
    public function getTotalCount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function incrementViewCount(string $ip, int $id): bool
    {
        $conn = $this->getEntityManager()->getConnection();
    
        $sqlCheck = 'SELECT COUNT(*) as cnt FROM post_views WHERE post_id = :id AND ip_address = :ip';
    
        $stmt = $conn->prepare($sqlCheck);
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
        $stmt->bindValue('ip', $ip, \PDO::PARAM_STR);
    
        $result = $stmt->executeQuery()->fetchAssociative();
    
        
        if ($result['cnt'] == 0) {
            
            $sqlInsert = 'INSERT INTO post_views (post_id, ip_address) VALUES (:id, :ip)';
            $stmt = $conn->prepare($sqlInsert);
            $stmt->bindValue('id', $id, \PDO::PARAM_INT);
            $stmt->bindValue('ip', $ip, \PDO::PARAM_STR);
            $stmt->executeQuery();
    
   
            $sqlUpdate = 'UPDATE posts SET view_count = view_count + 1 WHERE id = :id';
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bindValue('id', $id, \PDO::PARAM_INT);
            $res = $stmt->executeQuery();
    
            return $res !== false;
        }
    
        return false;
    }
    

    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
