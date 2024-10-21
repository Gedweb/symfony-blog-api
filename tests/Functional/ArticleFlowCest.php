<?php


namespace App\Tests\Functional;

use App\DataFixtures\ArticleFixture;
use App\Entity\Article;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Response;

class ArticleFlowCest
{
    const ARTICLE_TITLE_1 = 'hello';
    const ARTICLE_TITLE_2 = 'world';

    public function testUpsertArticle(FunctionalTester $I)
    {
        // create
        $article1 = $I->upsertArticle([
            'title' => self::ARTICLE_TITLE_1,
        ]);
        $I->assertIsNumeric($article1->getId());
        $articleRow = $I->grabEntityFromRepository(Article::class, ['id' => $article1->getId()]);
        $I->assertNotNull($articleRow);
        $I->assertEquals(self::ARTICLE_TITLE_1, $articleRow->getTitle());

        // update
        $article2 = $I->upsertArticle([
            'id' => $articleRow->getId(),
            'title' => self::ARTICLE_TITLE_2,
        ]);
        $I->assertEquals($article1->getId(), $article2->getId(), 'id still not changed');
        $I->assertEquals($article2->getTitle(), self::ARTICLE_TITLE_2);

        // delete
        $I->deleteArticle($article2->getId());
        $I->getArticle($article1->getId());
        $I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);
    }

    #[DataProvider('testArticleTagsDataProvider')]
    public function testArticleTags(FunctionalTester $I, Example $example): void
    {
        $expectedList = $example['tags'];
        sort($expectedList);
        $articleRow = $I->grabEntityFromRepository(Article::class, ['title' => ArticleFixture::TITLE1]);
        $I->assertNotNull($articleRow, 'fixture loaded');

        $article = $I->upsertArticle([
            'id' => $articleRow->getId(),
            'title' => $articleRow->getTitle(),
            'tags' => $expectedList,
        ]);

        $I->seeResponseCodeIs(Response::HTTP_OK, 'Entity was found');

        $resultList = $article?->getTags() ?? [];
        sort($resultList);
        $I->assertEquals($expectedList, $resultList, 'tags updated');
    }

    protected function testArticleTagsDataProvider(): array
    {

        return [
            ['tags' => [ArticleFixture::TAGS[0]]],
            ['tags' => ArticleFixture::TAGS],
            ['tags' => [ArticleFixture::TAGS[1]]],
        ];
    }

    public function tesArticletList(FunctionalTester $I)
    {
        $response = $I->getArticleList([
            'tags' => [ArticleFixture::UNIQUE_TAG],
        ]);
        $I->assertCount(1, $response->getList());
    }
}
