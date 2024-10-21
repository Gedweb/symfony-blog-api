<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function update(int $id, Tag $tag): Tag
    {
        $entity = $this->getEntityManager()->getReference($this->getEntityName(), $id);
        $entity->setName($tag->getName());
        $this->getEntityManager()->flush();

        return $entity;
    }

    public function save(Tag $entity): Tag
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }
}
