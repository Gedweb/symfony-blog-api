<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[Assert\GreaterThan(0)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    /**
     * @var Collection<int, ArticleTags>
     */
    #[JMS\Exclude]
    #[ORM\OneToMany(targetEntity: ArticleTags::class, mappedBy: 'article', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Collection $articleTags;

    #[JMS\Accessor(getter: 'getRelatedTags', setter: 'setTags')]
    #[JMS\Type('array<string>')]
    private array $tags = [];

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)]
    private string $title;

    public function __construct()
    {
        $this->articleTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getRelatedTags(): array
    {
        return iterator_to_array(
            $this->articleTags->map(fn(ArticleTags $entity) => $entity->getTag()->getName())
        );
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): static
    {
        $this->tags = array_unique($tags);

        return $this;
    }

    public function setArticleTags(Collection $articleTags): static
    {
        $this->articleTags = $articleTags;

        return $this;
    }

    public function getArticleTags(): Collection
    {
        return $this->articleTags;
    }
}
