<?php
declare(strict_types=1);

namespace App\Dto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ErrorMessage
{
    #[JMS\Groups(['debug'])]
    private int $line = 0;

    #[JMS\Groups(['debug'])]
    private string $file = '';

    #[JMS\Groups(['debug'])]
    private string $class = '';

    #[JMS\Groups(['debug'])]
    private string $trace = '';

    public function __construct(private readonly string $message, private readonly string $code = '')
    {
    }

    public static function fromThrowable(\Throwable $throwable): self
    {
        $self = new self($throwable->getMessage(), (string)$throwable->getCode());
        $self->line = $throwable->getLine();
        $self->file = $throwable->getFile();
        $self->class = $throwable::class;
        $self->trace = $throwable->getTraceAsString();

        return $self;
    }

    public static function fromViolation(ConstraintViolationInterface $violation): self
    {
        $self = new self($violation->getPropertyPath() . ' ' . $violation->getMessage(), (string)$violation->getCode());
        $self->class = $violation->getPropertyPath();

        return $self;
    }

    public static function create(string $string, string $code = ''): self
    {
        $self = new self($string, $code);
        $self->class = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]['class'] ?: '';

        return $self;
    }
}
