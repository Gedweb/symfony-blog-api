<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function getPage(int $id, int $limit, ?array $tags): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.id > :id')
            ->setParameter('id', $id)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults($limit);

        if ($tags !== null) {
            $qb->innerJoin('a.articleTags', 'at')
                ->andWhere('at.tag IN (:tags)')
                ->setParameter('tags', $tags);
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function delete(int $id): void
    {
        $entity = $this->getEntityManager()->getReference($this->getEntityName(), $id);
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function save(Article $entity): Article
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }
}