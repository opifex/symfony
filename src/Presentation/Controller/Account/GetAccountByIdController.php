<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\MessageHandler\GetAccountById\GetAccountByIdRequest;
use App\Domain\Account\AccountStatus;
use App\Domain\Common\HttpSpecification;
use App\Domain\Localization\LocaleCode;
use App\Domain\Security\Role;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class GetAccountByIdController extends AbstractController
{
    #[OA\Get(
        summary: 'Get account by identifier',
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
                response: Response::HTTP_OK,
                description: HttpSpecification::STATUS_OK,
                content: new OA\JsonContent(
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
                                enum: Role::class,
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
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: HttpSpecification::STATUS_UNAUTHORIZED,
            ),
        ],
    )]
    #[Route(
        path: '/account/{id}',
        name: 'app_get_account_by_id',
        methods: Request::METHOD_GET,
    )]
    public function __invoke(#[ValueResolver('payload')] GetAccountByIdRequest $request): Response
    {
        return $this->getHandledResult($request);
    }
}
