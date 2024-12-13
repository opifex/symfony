<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Attribute\MapMessage;
use App\Application\MessageHandler\GetAccountsByCriteria\GetAccountsByCriteriaRequest;
use App\Application\MessageHandler\GetAccountsByCriteria\GetAccountsByCriteriaResponse;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\AccountStatus;
use App\Domain\Entity\HttpSpecification;
use App\Domain\Entity\SortingOrder;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[AsController]
final class GetAccountsByCriteriaController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[OA\Get(
        summary: 'Get accounts by criteria',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(
                name: 'email',
                description: 'Account email template',
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
                name: 'sort',
                description: 'Sorting field name',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: AccountSearchCriteria::SORTING_FIELDS),
                example: AccountSearchCriteria::FIELD_CREATED_AT,
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Sorting order direction',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: SortingOrder::class),
                example: SortingOrder::Asc,
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
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: HttpSpecification::STATUS_BAD_REQUEST),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: HttpSpecification::STATUS_FORBIDDEN),
            new OA\Response(
                response: Response::HTTP_OK,
                description: HttpSpecification::STATUS_OK,
                headers: [
                    new OA\Header(
                        header: HttpSpecification::HEADER_X_TOTAL_COUNT,
                        description: 'Total count of items without limit',
                        schema: new OA\Schema(type: 'int', example: '10'),
                    ),
                ],
                content: new OA\JsonContent(
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
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: HttpSpecification::STATUS_UNAUTHORIZED),
        ],
    )]
    #[Route(
        path: '/account',
        name: 'app_get_accounts_by_criteria',
        methods: Request::METHOD_GET,
    )]
    #[IsGranted(AccountRole::Admin->value, message: 'Not privileged to request the resource.')]
    public function __invoke(#[MapMessage] GetAccountsByCriteriaRequest $message): Response
    {
        /** @var GetAccountsByCriteriaResponse $handledResult */
        $handledResult = $this->handle($message);

        return new JsonResponse(
            data: $this->normalizer->normalize($handledResult),
            headers: [HttpSpecification::HEADER_X_TOTAL_COUNT => count($handledResult)],
        );
    }
}
