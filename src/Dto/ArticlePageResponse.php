<?php
declare(strict_types=1);

namespace App\Dto;

use JMS\Serializer\Annotation as JMS;

class ArticlePageResponse
{
    public function __construct(
        #[JMS\Type('array<App\Entity\Article>')]
        private array $list = [],
    )
    {
    }

    public function getList(): array
    {
        return $this->list;
    }
}