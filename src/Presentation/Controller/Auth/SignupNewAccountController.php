<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Application\MessageHandler\SignupNewAccount\SignupNewAccountRequest;
use App\Domain\Model\HttpSpecification;
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
    #[OA\Post(
        summary: 'Signup new account',
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
                        default: 'en_US',
                        pattern: '[a-z]{2}_[A-Z]{2}',
                        example: 'en_US',
                    ),
                ],
                type: 'object',
            ),
        ),
        tags: ['Authorization'],
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
                response: Response::HTTP_NO_CONTENT,
                description: HttpSpecification::STATUS_NO_CONTENT,
            ),
        ],
    )]
    #[Route(
        path: '/auth/signup',
        name: 'app_signup_new_account',
        methods: Request::METHOD_POST,
    )]
    public function __invoke(#[ValueResolver('payload')] SignupNewAccountRequest $request): Response
    {
        return $this->getHandledResult($request);
    }
}
