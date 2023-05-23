<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Domain\Entity\AccountAction;
use App\Domain\Entity\AccountRole;
use App\Domain\Message\Account\ApplyAccountActionCommand;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
final class ApplyAccountActionController extends AbstractController
{
    #[OA\Post(
        summary: 'Apply account action',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(name: 'uuid', description: 'Account identifier', in: 'path'),
            new OA\Parameter(
                name: 'action',
                description: 'Action name',
                in: 'path',
                schema: new OA\Schema(type: 'string', enum: AccountAction::LIST),
            ),
        ],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Not Found'),
            new OA\Response(response: Response::HTTP_NO_CONTENT, description: 'No Content'),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ],
    )]
    #[Route(
        path: '/api/account/{uuid}/{action}',
        name: __CLASS__,
        methods: Request::METHOD_POST,
        format: 'json',
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(ApplyAccountActionCommand $message): Envelope
    {
        return $this->commandBus->dispatch($message);
    }
}
