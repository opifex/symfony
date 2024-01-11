<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Application\Attribute\MapMessage;
use App\Application\Handler\SigninIntoAccount\SigninIntoAccountCommand;
use App\Application\Messenger\ResponseStamp;
use App\Domain\Event\AccountAuthenticatedEvent;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[AsController]
final class SigninIntoAccountController extends AbstractController
{
    private ?AccountAuthenticatedEvent $accountAuthenticatedEvent = null;

    #[OA\Post(
        summary: 'Signin into account',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: SigninIntoAccountCommand::class),
            ),
        ),
        tags: ['Authorization'],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(
                response: Response::HTTP_NO_CONTENT,
                description: 'No Content',
                headers: [
                    new OA\Header(
                        header: 'Authorization',
                        description: 'Contains authorization jwt token',
                        schema: new OA\Schema(type: 'string', example: 'Bearer czZCaGRSa3F0Mzo3RmPmcDBa'),
                    ),
                ],
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ],
    )]
    #[Route(
        path: '/auth/signin',
        name: 'app_signin_into_account',
        methods: Request::METHOD_POST,
        format: JsonEncoder::FORMAT,
    )]
    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    public function __invoke(#[MapMessage] SigninIntoAccountCommand $message): Envelope
    {
        $this->eventDispatcher->addListener(
            eventName: AccountAuthenticatedEvent::class,
            listener: fn(AccountAuthenticatedEvent $event) => $this->accountAuthenticatedEvent = $event,
        );

        return $this->commandBus->dispatch($message)->with(
            new ResponseStamp(headers: [
                'Authorization' => 'Bearer ' . $this->accountAuthenticatedEvent?->accessToken->getSecret(),
            ]),
        );
    }
}
