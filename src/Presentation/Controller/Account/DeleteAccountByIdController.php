<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Attribute\MapMessage;
use App\Application\Handler\DeleteAccountById\DeleteAccountByIdCommand;
use App\Application\Handler\DeleteAccountById\DeleteAccountByIdResponse;
use App\Domain\Entity\AccountRole;
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
final class DeleteAccountByIdController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
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
        path: '/account/{uuid}',
        name: 'app_delete_account_by_id',
        methods: Request::METHOD_DELETE,
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(#[MapMessage] DeleteAccountByIdCommand $message): Response
    {
        /** @var DeleteAccountByIdResponse $handledResult */
        $handledResult = $this->handle($message);

        return new JsonResponse(
            data: $this->normalizer->normalize($handledResult),
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
