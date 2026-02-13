<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Query\GetAccountById\GetAccountByIdQuery;
use App\Domain\Account\AccountRole;
use App\Domain\Account\AccountStatus;
use App\Domain\Foundation\HttpSpecification;
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
final class GetAccountByIdController extends AbstractController
{
    #[OA\Get(summary: 'Get account by identifier', security: [['Bearer' => []]])]
    #[OA\Tag(name: 'Account')]
    #[OA\PathParameter(
        name: 'id',
        description: 'Account unique identifier',
        example: '00000000-0000-6000-8000-000000000000',
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_OK,
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
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_BAD_REQUEST,
        description: HttpSpecification::STATUS_BAD_REQUEST,
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_FORBIDDEN,
        description: HttpSpecification::STATUS_FORBIDDEN,
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_NOT_FOUND,
        description: HttpSpecification::STATUS_NOT_FOUND,
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_UNAUTHORIZED,
        description: HttpSpecification::STATUS_UNAUTHORIZED,
    )]
    #[IsGranted(attribute: 'ROLE_ADMIN')]
    #[Route(path: '/account/{id}', name: 'app_get_account_by_id', methods: Request::METHOD_GET)]
    public function __invoke(#[ValueResolver('payload')] GetAccountByIdQuery $query): Response
    {
        return $this->queryMessageBus->ask($query)->toResponse();
    }
}
