<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Query\GetAccountsByCriteria\GetAccountsByCriteriaQuery;
use App\Application\Query\GetAccountsByCriteria\GetAccountsByCriteriaQueryResult;
use App\Domain\Account\AccountRole;
use App\Domain\Account\AccountStatus;
use App\Domain\Localization\LocaleCode;
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
    #[OA\Get(summary: 'Get accounts by criteria', security: [['Bearer' => []]])]
    #[OA\Tag(name: 'Account')]
    #[OA\QueryParameter(
        name: 'email',
        description: 'Account email address',
        example: 'admin@example.com',
    )]
    #[OA\QueryParameter(
        name: 'status',
        description: 'Account status name',
        schema: new OA\Schema(type: 'string', enum: AccountStatus::class),
        example: AccountStatus::Activated,
    )]
    #[OA\QueryParameter(
        name: 'limit',
        description: 'Result items limit',
        example: 10,
    )]
    #[OA\QueryParameter(
        name: 'offset',
        description: 'Result items offset',
        example: 0,
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'OK',
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
                                property: 'id',
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
                                enum: LocaleCode::class,
                                example: LocaleCode::EnUs,
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
    )]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request')]
    #[OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[IsGranted(attribute: 'ROLE_ADMIN')]
    #[Route(path: '/account', name: 'app_get_accounts_by_criteria', methods: Request::METHOD_GET)]
    public function __invoke(#[ValueResolver('payload')] GetAccountsByCriteriaQuery $query): Response
    {
        /** @var GetAccountsByCriteriaQueryResult $handledResult */
        $handledResult = $this->queryMessageBus->ask($query);

        return $this->json($handledResult, status: Response::HTTP_OK);
    }
}
