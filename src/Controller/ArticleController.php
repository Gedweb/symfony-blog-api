<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\ArticlePageRequest;
use App\Dto\ErrorMessage;
use App\Dto\ArticlePageResponse;
use App\Dto\RestResponse;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use App\Resolver\Attribute\Dto;
use App\Resolver\Attribute\Query;
use App\Service\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[Route(path: 'api')]
class ArticleController extends AbstractController
{
    public function __construct(private ArticleRepository $articleRepository)
    {
    }

    #[Route('/article/{id}', methods: 'GET')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get An article with by id',
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Article hasn\'t found',
        content: new OA\JsonContent(ref: new Model(type: ErrorMessage::class))
    )]
    public function readOne(int $id): ?Article
    {
        return $this->articleRepository->find($id);
    }

    #[Route('/article', methods: 'GET')]
    #[OA\Parameter(name: 'id', description: 'cursor id', in: 'query')]
    #[OA\Parameter(name: 'limit', description: 'page size', in: 'query')]
    #[OA\Parameter(name: 'tags[]', description: 'article tags', in: 'query',
        schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')))
    ]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return page of articles',
        content: new OA\JsonContent(ref: new Model(type: ArticlePageResponse::class))
    )]
    public function readList(#[Query] ArticlePageRequest $cursor, TagRepository $tagRepository): ArticlePageResponse
    {

        return new ArticlePageResponse($this->articleRepository->getPage(
            $cursor->getId(),
            $cursor->getLimit(),
            count($cursor->getTags()) === 0 ? null : $tagRepository->findBy(['name' => $cursor->getTags()]),
        ));
    }

    #[Route('/article', methods: 'POST')]
    #[OA\RequestBody(
        description: 'Declare ID for update or keep it empty to create article',
        required: true,
        content: new Oa\JsonContent(ref: new Model(type: Article::class))
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Created or updated article ',
        content: new OA\JsonContent(ref: new Model(type: Article::class))
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Article hasn\'t found',
        content: new OA\JsonContent(ref: new Model(type: ErrorMessage::class))
    )]
    public function upsert(#[Dto] Article $dto, ArticleService $service): ?Article
    {
        if ($dto->getId() !== null) {
            return $service->update($dto);
        }

        return $this->articleRepository->save($dto);
    }

    #[Route('/article/{id}', methods: 'DELETE')]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Article has been deleted',
    )]
    public function delete(int $id): RestResponse
    {
        $this->articleRepository->delete($id);

        return RestResponse::new(Response::HTTP_NO_CONTENT);
    }
}
