<?php
declare(strict_types=1);

namespace App\Resolver;

use App\Dto\ErrorMessage;
use App\Exception\RestException;
use App\Resolver\Attribute\Dto;
use App\Resolver\Attribute\Query;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class DeserializeResolver implements ValueResolverInterface
{
    public function __construct(private Serializer $serializer, private ValidatorInterface $validator)
    {
    }

    /**
     * @return string[]
     */
    private function getDeserializationGroups(): array
    {
        return ['Default', 'input'];
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (false !== current($argument->getAttributes(Dto::class))) {
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                $argument->getType(),
                'json',
                DeserializationContext::create()->setGroups($this->getDeserializationGroups()),
            );
        } elseif (current($argument->getAttributes(Query::class))) {
            $dto = $this->serializer->fromArray($request->query->all(), $argument->getType());
        } else {
            return [];
        }

        $violations = $this->validator->validate($dto, groups: new GroupSequence($this->getDeserializationGroups()));
        if ($violations->count() > 0) {
            $restException = new RestException();
            foreach ($violations as $violation) {
                $restException->getRestResponse()->addError(ErrorMessage::fromViolation($violation));
            }
            throw $restException;
        }

        return [$dto];
    }
}
