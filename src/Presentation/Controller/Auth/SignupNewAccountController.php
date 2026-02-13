<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Application\Command\SignupNewAccount\SignupNewAccountCommand;
use App\Domain\Foundation\HttpSpecification;
use App\Domain\Localization\LocaleCode;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class SignupNewAccountController extends AbstractController
{
    #[OA\Post(summary: 'Signup new account')]
    #[OA\Tag(name: 'Authorization')]
    #[OA\RequestBody(
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
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_BAD_REQUEST,
        description: HttpSpecification::STATUS_BAD_REQUEST,
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_CONFLICT,
        description: HttpSpecification::STATUS_CONFLICT,
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_NO_CONTENT,
        description: HttpSpecification::STATUS_NO_CONTENT,
    )]
    #[Route(path: '/auth/signup', name: 'app_signup_new_account', methods: Request::METHOD_POST)]
    public function __invoke(#[ValueResolver('payload')] SignupNewAccountCommand $command): Response
    {
        return $this->commandMessageBus->dispatch($command)->toResponse();
    }
}
