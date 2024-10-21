<?php
declare(strict_types=1);

namespace App\Dto;

use JMS\Serializer\Annotation as JMS;

class ArticlePageRequest extends PageRequest
{
    #[JMS\Type('array<string>')]
    protected array $tags = [];

    public function getTags(): array
    {
        return $this->tags;
    }
}