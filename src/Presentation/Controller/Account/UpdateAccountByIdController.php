<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Domain\Entity\Account\AccountRole;
use App\Domain\Message\Account\UpdateAccountByIdCommand;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
final class UpdateAccountByIdController extends AbstractController
{
    #[OA\Patch(
        summary: 'Update account by identifier',
        security: [['bearer' => []]],
        requestBody: new RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: UpdateAccountByIdCommand::class,
                    groups: [UpdateAccountByIdCommand::GROUP_EDITABLE],
                ),
            ),
        ),
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
        methods: Request::METHOD_PATCH,
        format: 'json',
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(UpdateAccountByIdCommand $message): Envelope
    {
        return $this->commandBus->dispatch($message);
    }
}
