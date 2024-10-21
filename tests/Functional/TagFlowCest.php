<?php


namespace App\Tests\Functional;

use App\Entity\Tag;
use App\Tests\Support\FunctionalTester;
use \Codeception\Example;

class TagFlowCest
{
    const TAG_NAME_1 = 'lorem';
    const TAG_NAME_2 = 'ipsum';

    public function testUpsertTag(FunctionalTester $I)
    {
        $tag1 = $I->upsertTag([
            'name' => self::TAG_NAME_1,
        ]);
        $I->assertIsNumeric($tag1->getId());
        $tagRow = $I->grabEntityFromRepository(Tag::class, ['id' => $tag1->getId()]);
        $I->assertNotNull($tagRow);
        $I->assertEquals(self::TAG_NAME_1, $tagRow->getName());

        $tag2 = $I->upsertTag([
            'id' => $tagRow->getId(),
            'name' => self::TAG_NAME_2,
        ]);
        $I->assertEquals($tag1->getId(), $tag2->getId(), 'id still not changed');
        $I->assertEquals($tag2->getName(), self::TAG_NAME_2);
    }
}
