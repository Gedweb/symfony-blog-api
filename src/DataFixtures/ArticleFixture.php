<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\ArticleTags;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixture extends Fixture
{
    const TAGS = [
        'contribution',
        'big-day',
        'brain-dance.',
        'shopping',
        'mistake',
    ];

    const UNIQUE_TAG = 'special-article';

    const TITLE1 = 'House prices';
    const TITLE2 = 'Social media post';

    public function load(ObjectManager $manager): void
    {
        foreach (self::TAGS as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $manager->persist($tag);
        }

        $article1 = new Article();
        $article1->setTitle(self::TITLE1);
        $manager->persist($article1);

        $article2 = new Article();
        $article2->setTitle(self::TITLE2);

        $uniqueTag = new Tag();
        $uniqueTag->setName(self::UNIQUE_TAG);
        $manager->persist($uniqueTag);

        $article2->getArticleTags()->add(new ArticleTags($article2, $uniqueTag));
        $manager->persist($article2);

        $manager->flush();
    }
}
