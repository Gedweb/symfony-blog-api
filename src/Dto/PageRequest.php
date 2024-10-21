<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PageRequest
{
    public const MAX_PAGE_SIZE = 20;

    private int $id = 0;

    #[Assert\Range(min: 1, max: self::MAX_PAGE_SIZE)]
    private int $limit = self::MAX_PAGE_SIZE;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}