<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\MessageHandler\Command\UpdateAccountById\UpdateAccountByIdCommand;
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
final class UpdateAccountByIdController extends AbstractController
{
    #[OA\Patch(
        summary: 'Update account by identifier',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'email',
                        example: 'user@example.com',
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'password',
                        maxLength: 32,
                        minLength: 8,
                        example: 'password4#account',
                    ),
                    new OA\Property(
                        property: 'locale',
                        type: 'string',
                        default: LocaleCode::EnUs,
                        enum: LocaleCode::class,
                        example: LocaleCode::EnUs,
                    ),
                ],
                type: 'object',
            ),
        ),
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
                response: HttpSpecification::HTTP_BAD_REQUEST,
                description: HttpSpecification::STATUS_BAD_REQUEST,
            ),
            new OA\Response(
                response: HttpSpecification::HTTP_FORBIDDEN,
                description: HttpSpecification::STATUS_FORBIDDEN,
            ),
            new OA\Response(
                response: HttpSpecification::HTTP_NOT_FOUND,
                description: HttpSpecification::STATUS_NOT_FOUND,
            ),
            new OA\Response(
                response: HttpSpecification::HTTP_NO_CONTENT,
                description: HttpSpecification::STATUS_NO_CONTENT,
            ),
            new OA\Response(
                response: HttpSpecification::HTTP_UNAUTHORIZED,
                description: HttpSpecification::STATUS_UNAUTHORIZED,
            ),
        ],
    )]
    #[IsGranted(attribute: 'ROLE_ADMIN')]
    #[Route(path: '/account/{id}', name: 'app_update_account_by_id', methods: Request::METHOD_PATCH)]
    public function __invoke(#[ValueResolver('payload')] UpdateAccountByIdCommand $request): Response
    {
        return $this->commandMessageBus->dispatch($request)->toResponse();
    }
}
