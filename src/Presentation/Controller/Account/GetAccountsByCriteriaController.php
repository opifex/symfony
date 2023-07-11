<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Attribute\MapRequestMessage;
use App\Application\Handler\GetAccountsByCriteria\GetAccountsByCriteriaItem;
use App\Application\Handler\GetAccountsByCriteria\GetAccountsByCriteriaQuery;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

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
                description: 'Account email template',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Account status name',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: AccountStatus::LIST),
            ),
            new OA\Parameter(
                name: 'sort',
                description: 'Sorting field name',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: ['created_at', 'email', 'status']),
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Sorting order direction',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']),
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
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                headers: [
                    new OA\Header(
                        header: 'X-Total-Count',
                        description: 'Total count of items without limit',
                        schema: new OA\Schema(type: 'int', example: '10'),
                    ),
                ],
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: GetAccountsByCriteriaItem::class)),
                ),
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ],
    )]
    #[Route(
        path: '/api/account',
        name: __CLASS__,
        methods: Request::METHOD_GET,
        format: JsonEncoder::FORMAT,
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(#[MapRequestMessage] GetAccountsByCriteriaQuery $message): Envelope
    {
        return $this->queryBus->dispatch($message);
    }
}
