<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\MessageHandler\Command\UnblockAccountById\UnblockAccountByIdCommand;
use App\Domain\Foundation\HttpSpecification;
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
        response: HttpSpecification::HTTP_NO_CONTENT,
        description: HttpSpecification::STATUS_NO_CONTENT,
    )]
    #[OA\Response(
        response: HttpSpecification::HTTP_UNAUTHORIZED,
        description: HttpSpecification::STATUS_UNAUTHORIZED,
    )]
    #[IsGranted(attribute: 'ROLE_ADMIN')]
    #[Route(path: '/account/{id}/unblock', name: 'app_unblock_account_by_id', methods: Request::METHOD_POST)]
    public function __invoke(#[ValueResolver('payload')] UnblockAccountByIdCommand $command): Response
    {
        return $this->commandMessageBus->dispatch($command)->toResponse();
    }
}
