<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Domain\Message\SignupNewAccountCommand;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class SignupNewAccountController extends AbstractController
{
    #[OA\Post(
        summary: 'Signup new account',
        requestBody: new RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: SignupNewAccountCommand::class),
            ),
        ),
        tags: ['Authorization'],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_CONFLICT, description: 'Conflict'),
            new OA\Response(response: Response::HTTP_NO_CONTENT, description: 'No Content'),
        ],
    )]
    #[Route(
        path: '/api/auth/signup',
        name: __CLASS__,
        methods: Request::METHOD_POST,
        format: 'json',
    )]
    public function __invoke(SignupNewAccountCommand $message): Envelope
    {
        return $this->commandBus->dispatch($message);
    }
}
