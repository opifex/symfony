<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\MessageHandler\BlockAccountById\BlockAccountByIdRequest;
use App\Domain\Common\HttpSpecification;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class BlockAccountByIdController extends AbstractController
{
    #[OA\Post(
        summary: 'Block account by identifier',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Account unique identifier',
                in: 'path',
                example: '00000000-0000-6000-8000-000000000000',
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: HttpSpecification::STATUS_BAD_REQUEST,
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: HttpSpecification::STATUS_FORBIDDEN,
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: HttpSpecification::STATUS_NOT_FOUND,
            ),
            new OA\Response(
                response: Response::HTTP_NO_CONTENT,
                description: HttpSpecification::STATUS_NO_CONTENT,
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: HttpSpecification::STATUS_UNAUTHORIZED,
            ),
        ],
    )]
    #[Route(
        path: '/account/{id}/block',
        name: 'app_block_account_by_id',
        methods: Request::METHOD_POST,
    )]
    public function __invoke(#[ValueResolver('payload')] BlockAccountByIdRequest $request): Response
    {
        return $this->getHandledResult($request);
    }
}
