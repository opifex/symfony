<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Attribute\MapMessage;
use App\Application\MessageHandler\UnblockAccountById\UnblockAccountByIdRequest;
use App\Application\MessageHandler\UnblockAccountById\UnblockAccountByIdResponse;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\HttpSpecification;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[AsController]
final class UnblockAccountByIdController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[OA\Post(
        summary: 'Unblock account by identifier',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'Account identifier',
                in: 'path',
                example: '00000000-0000-6000-8000-000000000000',
            ),
        ],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: HttpSpecification::STATUS_BAD_REQUEST),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: HttpSpecification::STATUS_FORBIDDEN),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: HttpSpecification::STATUS_NOT_FOUND),
            new OA\Response(response: Response::HTTP_NO_CONTENT, description: HttpSpecification::STATUS_NO_CONTENT),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: HttpSpecification::STATUS_UNAUTHORIZED),
        ],
    )]
    #[Route(
        path: '/account/{uuid}/unblock',
        name: 'app_unblock_account_by_id',
        methods: Request::METHOD_POST,
    )]
    #[IsGranted(AccountRole::Admin->value, message: 'Not privileged to request the resource.')]
    public function __invoke(#[MapMessage] UnblockAccountByIdRequest $message): Response
    {
        /** @var UnblockAccountByIdResponse $handledResult */
        $handledResult = $this->handle($message);

        return new JsonResponse(
            data: $this->normalizer->normalize($handledResult),
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
