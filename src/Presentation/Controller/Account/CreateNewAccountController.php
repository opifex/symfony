<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Command\CreateNewAccount\CreateNewAccountCommand;
use App\Domain\Localization\LocaleCode;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
final class CreateNewAccountController extends AbstractController
{
    #[OA\Post(summary: 'Create new account', security: [['Bearer' => []]])]
    #[OA\Tag(name: 'Account')]
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
        response: Response::HTTP_CREATED,
        description: 'Created',
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
    )]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request')]
    #[OA\Response(response: Response::HTTP_CONFLICT, description: 'Conflict')]
    #[OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[IsGranted(attribute: 'ROLE_ADMIN')]
    #[Route(path: '/account', name: 'app_create_new_account', methods: Request::METHOD_POST)]
    public function __invoke(#[ValueResolver('payload')] CreateNewAccountCommand $command): Response
    {
        return new JsonResponse(
            data: $this->commandMessageBus->dispatch($command)->getPayload(),
            status: Response::HTTP_CREATED,
        );
    }
}
