<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleTagsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleTagsRepository::class)]
class ArticleTags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $udpatedAt;

    public function __construct(

        #[ORM\ManyToOne(inversedBy: 'articleTags')]
        #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')]
        private Article $article,

        #[ORM\ManyToOne(inversedBy: 'articleTags')]
        #[ORM\JoinColumn(name: 'tag_id', referencedColumnName: 'id')]
        private Tag     $tag,
    )
    {
        $this->udpatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    public function getUdpatedAt(): ?\DateTimeImmutable
    {
        return $this->udpatedAt;
    }

    public function setUdpatedAt(\DateTimeImmutable $udpatedAt): static
    {
        $this->udpatedAt = $udpatedAt;

        return $this;
    }
}
