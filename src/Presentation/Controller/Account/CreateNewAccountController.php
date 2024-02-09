<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Attribute\MapMessage;
use App\Application\Handler\CreateNewAccount\CreateNewAccountCommand;
use App\Application\Messenger\ResponseStamp;
use App\Domain\Entity\AccountRole;
use App\Domain\Event\AccountRegisteredEvent;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[AsController]
final class CreateNewAccountController extends AbstractController
{
    private ?AccountRegisteredEvent $accountRegisteredEvent = null;

    #[OA\Post(
        summary: 'Create new account',
        security: [['bearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: CreateNewAccountCommand::class),
            ),
        ),
        tags: ['Account'],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_CONFLICT, description: 'Conflict'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(
                response: Response::HTTP_NO_CONTENT,
                description: 'No Content',
                headers: [
                    new OA\Header(
                        header: 'Location',
                        description: 'Contains url for created account',
                        schema: new OA\Schema(
                            type: 'string',
                            example: '/account/1ed49f4d-582c-6fbe-bd13-cfd1e04cb3c6',
                        ),
                    ),
                ],
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ],
    )]
    #[Route(
        path: '/account',
        name: 'app_create_new_account',
        methods: Request::METHOD_POST,
        format: JsonEncoder::FORMAT,
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(#[MapMessage] CreateNewAccountCommand $message): Envelope
    {
        $this->eventDispatcher->addListener(
            eventName: AccountRegisteredEvent::class,
            listener: fn(AccountRegisteredEvent $event) => $this->accountRegisteredEvent = $event,
        );

        return $this->commandBus->dispatch($message)->with(
            new ResponseStamp(headers: [
                'Location' => $this->urlGenerator->generate(
                    name: 'app_get_account_by_id',
                    parameters: ['uuid' => $this->accountRegisteredEvent?->account->getUuid()],
                ),
            ]),
        );
    }
}
