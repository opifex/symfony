<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Domain\Entity\Account\AccountRole;
use App\Domain\Message\Account\DeleteAccountByIdCommand;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[AsController]
class DeleteAccountByIdController extends AbstractController
{
    #[OA\Delete(
        summary: 'Delete account by identifier',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [new OA\Parameter(name: 'uuid', description: 'Account identifier', in: 'path')],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Not Found'),
            new OA\Response(response: Response::HTTP_NO_CONTENT, description: 'No Content'),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ],
    )]
    #[Route(
        path: '/api/account/{uuid}',
        name: __CLASS__,
        methods: Request::METHOD_DELETE,
        format: JsonEncoder::FORMAT,
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(DeleteAccountByIdCommand $message): Envelope
    {
        return $this->commandBus->dispatch($message);
    }
}
