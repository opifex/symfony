<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Application\Command\SignoutFromAccount\SignoutFromAccountCommand;
use App\Application\Command\SignoutFromAccount\SignoutFromAccountCommandResult;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
final class SignoutFromAccountController extends AbstractController
{
    #[OA\Post(summary: 'Signout from account', security: [['Bearer' => []]])]
    #[OA\Tag(name: 'Authorization')]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'No Content',
    )]
    #[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
    #[Route(path: '/auth/signout', name: 'app_signout_from_account', methods: Request::METHOD_POST)]
    public function __invoke(#[ValueResolver('payload')] SignoutFromAccountCommand $command): Response
    {
        /** @var SignoutFromAccountCommandResult $handledResult */
        $handledResult = $this->commandMessageBus->dispatch($command);

        return $this->json($handledResult, status: Response::HTTP_NO_CONTENT);
    }
}
