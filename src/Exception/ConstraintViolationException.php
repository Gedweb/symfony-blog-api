<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationException extends \InvalidArgumentException implements \Countable
{
    /**
     * @var string[]
     */
    private ConstraintViolationListInterface $violationsList;

    public static function new(ConstraintViolationListInterface $violationsList, $code = Response::HTTP_BAD_REQUEST, \Throwable $previous = null): self
    {
        $self = new self('validation failed', $code, $previous);
        $self->violationsList = $violationsList;

        return $self;
    }

    public function getViolationsList(): ConstraintViolationListInterface
    {
        return $this->violationsList;
    }

    public function count(): int
    {
        return count($this->violationsList);
    }
}
