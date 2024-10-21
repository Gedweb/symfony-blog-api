<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Entity\ArticleTags;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticleTags>
 */
class ArticleTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleTags::class);
    }
}
