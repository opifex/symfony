<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Message\Auth\SigninIntoAccountCommand;
use App\Domain\Messenger\ResponseStamp;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[AsController]
class SigninIntoAccountController extends AbstractController
{
    #[OA\Post(
        summary: 'Signin into account',
        requestBody: new RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: SigninIntoAccountCommand::class, groups: [MessageInterface::GROUP_BODY]),
            ),
        ),
        tags: ['Authorization'],
        responses: [
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
        path: '/api/auth/signin',
        name: __CLASS__,
        methods: [Request::METHOD_POST],
        format: JsonEncoder::FORMAT,
    )]
    public function __invoke(SigninIntoAccountCommand $message): Envelope
    {
        return $this->commandBus->dispatch($message)->with(
            new ResponseStamp(headers: [
                'Authorization' => 'Bearer ' . $this->tokenStorage->getToken(),
            ]),
        );
    }
}
