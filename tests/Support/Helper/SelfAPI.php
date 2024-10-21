<?php

declare(strict_types=1);

namespace App\Tests\Support\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\Dto\ArticlePageResponse;
use App\Entity\Article;
use App\Entity\Tag;
use Codeception\Module\REST;
use Codeception\TestInterface;
use JMS\Serializer\Serializer;

class SelfAPI extends \Codeception\Module
{
    const FORMAT = 'json';

    private Serializer $serializer;

    public function _before(TestInterface $test)
    {
        $this->serializer = $this->getModule('Symfony')->grabService(Serializer::class);
        parent::_before($test);
    }

    public function upsertTag(array $params)
    {
        $data = $this->getRest()->sendPost('/tag', $params);

        return $this->serializer->deserialize($data, Tag::class, self::FORMAT);
    }

    public function upsertArticle(array $params)
    {
        $data = $this->getRest()->sendPost('/article', $params);

        return $this->serializer->deserialize($data, Article::class, self::FORMAT);
    }

    public function getArticle(int $id): Article
    {
        $data = $this->getRest()->sendGet(sprintf('/article/%d', $id));

        return $this->serializer->deserialize($data, Article::class, self::FORMAT);
    }

    public function getArticleList(array $params): ArticlePageResponse
    {
        $data = $this->getRest()->sendGet('/article', $params);

        return $this->serializer->deserialize($data, ArticlePageResponse::class, self::FORMAT);
    }

    public function deleteArticle(int $id): void
    {
        $this->getRest()->sendDelete(sprintf('/article/%d', $id));
    }

    private function getRest(): REST
    {
        $rest = $this->getModule('REST');
        $rest->haveHttpHeader('content-type', 'application/json');

        return $rest;
    }
}
