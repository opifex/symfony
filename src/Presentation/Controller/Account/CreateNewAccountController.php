<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\MessageHandler\CreateNewAccount\CreateNewAccountRequest;
use App\Domain\Common\HttpSpecification;
use App\Domain\Common\LocaleCode;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class CreateNewAccountController extends AbstractController
{
    #[OA\Post(
        summary: 'Create new account',
        security: [['bearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
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
        responses: [
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: HttpSpecification::STATUS_BAD_REQUEST,
            ),
            new OA\Response(
                response: Response::HTTP_CONFLICT,
                description: HttpSpecification::STATUS_CONFLICT,
            ),
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: HttpSpecification::STATUS_CREATED,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'id',
                            type: 'string',
                            example: '00000000-0000-6000-8000-000000000000',
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: HttpSpecification::STATUS_FORBIDDEN,
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: HttpSpecification::STATUS_UNAUTHORIZED,
            ),
        ],
    )]
    #[Route(
        path: '/account',
        name: 'app_create_new_account',
        methods: Request::METHOD_POST,
    )]
    public function __invoke(#[ValueResolver('payload')] CreateNewAccountRequest $request): Response
    {
        return $this->getHandledResult($request);
    }
}
