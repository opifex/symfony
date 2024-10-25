<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Attribute\MapMessage;
use App\Application\MessageHandler\GetAccountsByCriteria\GetAccountsByCriteriaItem;
use App\Application\MessageHandler\GetAccountsByCriteria\GetAccountsByCriteriaRequest;
use App\Application\MessageHandler\GetAccountsByCriteria\GetAccountsByCriteriaResponse;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\AccountStatus;
use App\Domain\Entity\HttpSpecification;
use App\Domain\Entity\SortingOrder;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Attribute\Model;
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
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Account status name',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: AccountStatus::STATUSES),
            ),
            new OA\Parameter(
                name: 'sort',
                description: 'Sorting field name',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: AccountSearchCriteria::SORTING_FIELDS),
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Sorting order direction',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: SortingOrder::class),
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Result items limit',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'offset',
                description: 'Result items offset',
                in: 'query',
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
                    items: new OA\Items(ref: new Model(type: GetAccountsByCriteriaItem::class)),
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
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(#[MapMessage] GetAccountsByCriteriaRequest $message): Response
    {
        /** @var GetAccountsByCriteriaResponse $handledResult */
        $handledResult = $this->handle($message);

        return new JsonResponse(
            data: $this->normalizer->normalize($handledResult),
            headers: [
                HttpSpecification::HEADER_X_TOTAL_COUNT => count($handledResult),
            ],
        );
    }
}
