<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\ErrorMessage;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Resolver\Attribute\Dto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[Route(path: 'api')]
class TagController extends AbstractController
{
    public function __construct(private TagRepository $tagRepository)
    {
    }

    #[Route('/tag', methods: 'POST')]
    #[OA\RequestBody(
        description: 'tag upsert',
        required: true,
        content: new Oa\JsonContent(ref: new Model(type: Tag::class))
    )]
    #[OA\Response(
        response: 200,
        description: 'Created or updated tag',
        content: new OA\JsonContent(ref: new Model(type: Tag::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Tag hasn\'t found',
        content: new OA\JsonContent(ref: new Model(type: ErrorMessage::class))
    )]
    public function edit(#[Dto] Tag $dto): ?Tag
    {
        if ($dto->getId() !== null) {
            return $this->tagRepository->update($dto->getId(), $dto);
        }

        if ($dto->getName() !== null) {}

        return $this->tagRepository->save($dto);
    }
}
