<?php
declare(strict_types=1);

namespace App\Service;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

class JmsObjectConstructor implements ObjectConstructorInterface
{
    public function construct(
        DeserializationVisitorInterface $visitor,
        ClassMetadata $metadata,
        $data,
        array $type,
        DeserializationContext $context
    ): ?object {
        $reflectionClass = new \ReflectionClass($metadata->name);
        if (
            null !== $reflectionClass->getConstructor()
            && $reflectionClass->getConstructor()->getNumberOfRequiredParameters() > 0
        ) {
            throw new \InvalidArgumentException(sprintf('"%s" contains required arguments in constructor', $metadata->name));
        }

        return $reflectionClass->newInstance();
    }
}
