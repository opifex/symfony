<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Command\DeleteAccountById\DeleteAccountByIdCommand;
use App\Application\Command\DeleteAccountById\DeleteAccountByIdCommandResult;
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
final class DeleteAccountByIdController extends AbstractController
{
    #[OA\Delete(summary: 'Delete account by identifier', security: [['Bearer' => []]])]
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
    #[Route(path: '/account/{id}', name: 'app_delete_account_by_id', methods: Request::METHOD_DELETE)]
    public function __invoke(#[ValueResolver('payload')] DeleteAccountByIdCommand $command): Response
    {
        /** @var DeleteAccountByIdCommandResult $handledResult */
        $handledResult = $this->commandMessageBus->dispatch($command);

        return new JsonResponse(
            data: $handledResult->getPayload(),
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
