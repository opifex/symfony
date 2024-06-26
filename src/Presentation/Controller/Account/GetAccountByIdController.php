<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Application\Attribute\MapMessage;
use App\Application\MessageHandler\GetAccountById\GetAccountByIdRequest;
use App\Application\MessageHandler\GetAccountById\GetAccountByIdResponse;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\HttpSpecification;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[AsController]
final class GetAccountByIdController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[OA\Get(
        summary: 'Get account by identifier',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [new OA\Parameter(name: 'uuid', description: 'Account identifier', in: 'path')],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: HttpSpecification::STATUS_BAD_REQUEST),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: HttpSpecification::STATUS_FORBIDDEN),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: HttpSpecification::STATUS_NOT_FOUND),
            new OA\Response(
                response: Response::HTTP_OK,
                description: HttpSpecification::STATUS_OK,
                content: new OA\JsonContent(ref: new Model(type: GetAccountByIdResponse::class)),
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: HttpSpecification::STATUS_UNAUTHORIZED),
        ],
    )]
    #[Route(
        path: '/account/{uuid}',
        name: 'app_get_account_by_id',
        methods: Request::METHOD_GET,
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(#[MapMessage] GetAccountByIdRequest $message): Response
    {
        /** @var GetAccountByIdResponse $handledResult */
        $handledResult = $this->handle($message);

        return new JsonResponse(
            data: $this->normalizer->normalize($handledResult),
        );
    }
}
