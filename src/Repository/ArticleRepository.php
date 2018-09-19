<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByAuthor($value = null)
    {
        /*
        $sql = '
        SELECT * FROM`article`
        LEFT JOIN `article_author` ON `article_author`.`article_id` = `article`.`id`
        LEFT JOIN `author` ON `author`.`id` = `article_author`.`author_id`
        WHERE `author_id` IS NOT NULL';

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
        */

        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.author', 'author');

        if (empty($value)) {
            $queryBuilder->andWhere('author.id IS NOT NULL');
        }
        else {
            $queryBuilder->andWhere('author.id = :author_id')
                ->setParameter('author_id', $value);
        }

        return $queryBuilder
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByTag($value = null)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.tags', 'tags');

        if (empty($value)) {
            $queryBuilder->andWhere('tags.id IS NOT NULL');
        }
        else {
            $queryBuilder->andWhere('tags.id = :tag_id')
                ->setParameter('tag_id', $value);
        }

        return $queryBuilder
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
