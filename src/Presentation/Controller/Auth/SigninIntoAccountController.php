<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Application\Command\SigninIntoAccount\SigninIntoAccountCommand;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class SigninIntoAccountController extends AbstractController
{
    #[OA\Post(summary: 'Signin into account')]
    #[OA\Tag(name: 'Authorization')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'email',
                    example: 'admin@example.com',
                ),
                new OA\Property(
                    property: 'password',
                    type: 'password',
                    example: 'password4#account',
                ),
            ],
            type: 'object',
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'OK',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'access_token',
                    type: 'string',
                    example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJuYmYiOjE3MzQwODgyMTguOTU5ODI0...',
                ),
            ],
            type: 'object',
        ),
    )]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request')]
    #[OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[Route(path: '/auth/signin', name: 'app_signin_into_account', methods: Request::METHOD_POST)]
    public function __invoke(#[ValueResolver('payload')] SigninIntoAccountCommand $command): Response
    {
        return new JsonResponse(
            data: $this->commandMessageBus->dispatch($command)->getPayload(),
            status: Response::HTTP_OK,
        );
    }
}
