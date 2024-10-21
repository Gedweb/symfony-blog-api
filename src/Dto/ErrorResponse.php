<?php

declare(strict_types=1);

namespace App\Dto;

use JMS\Serializer\Annotation as JMS;

final class ErrorResponse implements \Countable
{
    /**
     * @var ErrorMessage[]
     */
    #[JMS\Type('array<App\Dto\ErrorMessage>')]
    public array $errors = [];

    public function count(): int
    {
        return count($this->errors);
    }

    public function pushError(ErrorMessage $errorMessage): self
    {
        $this->errors[] = $errorMessage;

        return $this;
    }
}
