<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Command\UnblockAccountById\UnblockAccountByIdCommand;
use App\Application\Command\UnblockAccountById\UnblockAccountByIdCommandResult;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
final class UnblockAccountByIdController extends AbstractController
{
    #[OA\Post(summary: 'Unblock account by identifier', security: [['Bearer' => []]])]
    #[OA\Tag(name: 'Account')]
    #[OA\PathParameter(
        name: 'id',
        description: 'Account unique identifier',
        example: '00000000-0000-6000-8000-000000000000',
    )]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request')]
    #[OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Not Found')]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'No Content')]
    #[OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[IsGranted(attribute: 'ROLE_ADMIN')]
    #[Route(path: '/account/{id}/unblock', name: 'app_unblock_account_by_id', methods: Request::METHOD_POST)]
    public function __invoke(#[ValueResolver('payload')] UnblockAccountByIdCommand $command): Response
    {
        /** @var UnblockAccountByIdCommandResult $handledResult */
        $handledResult = $this->commandMessageBus->dispatch($command);

        return $this->json($handledResult, status: Response::HTTP_NO_CONTENT);
    }
}
