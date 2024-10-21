<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\ArticleTags;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;

class ArticleService
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly TagRepository     $tagRepository,
    )
    {
    }

    public function update(Article $dto): ?Article
    {
        /** @var Article $article */
        $article = $this->articleRepository->find($dto->getId());
        if ($article === null) {
            return null;
        }

        $article->setTitle($dto->getTitle());
        $deleteTags = array_diff($article->getRelatedTags(), $dto->getTags());
        foreach ($article->getArticleTags() as $articleTag) {
            if (in_array($articleTag->getTag()->getName(), $deleteTags, true)) {
                $article->getArticleTags()->removeElement($articleTag);
            }
        }

        $createTags = array_diff($dto->getTags(), $article->getRelatedTags());
        foreach ($this->tagRepository->findBy(['name' => $createTags]) as $tag) {
            $article->getArticleTags()->add(new ArticleTags($article, $tag));
        }

        return $this->articleRepository->save($article);

    }
}