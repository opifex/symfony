<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\MessageHandler\GetAccountsByCriteria\GetAccountsByCriteriaRequest;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Domain\Entity\HttpSpecification;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
final class GetAccountsByCriteriaController extends AbstractController
{
    #[OA\Get(
        summary: 'Get accounts by criteria',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(
                name: 'email',
                description: 'Account email address',
                in: 'query',
                example: 'admin@example.com',
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Account status name',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: AccountStatus::class),
                example: AccountStatus::Activated,
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Result items limit',
                in: 'query',
                example: 10,
            ),
            new OA\Parameter(
                name: 'offset',
                description: 'Result items offset',
                in: 'query',
                example: 0,
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
                response: Response::HTTP_OK,
                description: HttpSpecification::STATUS_OK,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(
                                    property: 'current_page',
                                    type: 'integer',
                                    example: 1,
                                ),
                                new OA\Property(
                                    property: 'items_per_page',
                                    type: 'integer',
                                    example: 10,
                                ),
                                new OA\Property(
                                    property: 'total_items',
                                    type: 'integer',
                                    example: 100,
                                ),
                            ],
                            type: 'object',
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: 'uuid',
                                        type: 'uuid',
                                        example: '00000000-0000-6000-8000-000000000000',
                                    ),
                                    new OA\Property(
                                        property: 'email',
                                        type: 'email',
                                        example: 'admin@example.com',
                                    ),
                                    new OA\Property(
                                        property: 'locale',
                                        type: 'string',
                                        example: 'en_US',
                                    ),
                                    new OA\Property(
                                        property: 'status',
                                        type: 'string',
                                        enum: AccountStatus::class,
                                        example: AccountStatus::Activated,
                                    ),
                                    new OA\Property(
                                        property: 'roles',
                                        type: 'array',
                                        items: new OA\Items(
                                            type: 'string',
                                            enum: AccountRole::class,
                                        ),
                                    ),
                                    new OA\Property(
                                        property: 'created_at',
                                        type: 'string',
                                        example: '2025-01-01T12:00:00+00:00',
                                    ),
                                ],
                                type: 'object',
                            ),
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: HttpSpecification::STATUS_UNAUTHORIZED,
            ),
        ],
    )]
    #[Route(
        path: '/account',
        name: 'app_get_accounts_by_criteria',
        methods: Request::METHOD_GET,
    )]
    #[IsGranted(AccountRole::Admin->value, message: 'Not privileged to request the resource.')]
    public function __invoke(#[ValueResolver('payload')] GetAccountsByCriteriaRequest $message): Response
    {
        return $this->getHandledResult($message);
    }
}
