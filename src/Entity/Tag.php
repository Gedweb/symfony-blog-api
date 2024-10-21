<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
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
    #[ORM\OneToMany(targetEntity: ArticleTags::class, mappedBy: 'tag')]
    private Collection $articleTags;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)]
    private string $name;

    public function __construct()
    {
        $this->articleTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $getId): static
    {
        $this->id = $getId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, ArticleTags>
     */
    public function getArticleTags(): Collection
    {
        return $this->articleTags;
    }

    public function addArticleTag(ArticleTags $articleTag): static
    {
        if (!$this->articleTags->contains($articleTag)) {
            $this->articleTags->add($articleTag);
            $articleTag->setTag($this);
        }

        return $this;
    }

    public function removeArticleTag(ArticleTags $articleTag): static
    {
        if ($this->articleTags->removeElement($articleTag)) {
            // set the owning side to null (unless already changed)
            if ($articleTag->getTag() === $this) {
                $articleTag->setTag(null);
            }
        }

        return $this;
    }
}
