<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Application\Attribute\MapMessage;
use App\Application\MessageHandler\GetSigninAccount\GetSigninAccountRequest;
use App\Application\MessageHandler\GetSigninAccount\GetSigninAccountResponse;
use App\Domain\Entity\HttpSpecification;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Attribute as AD;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[AsController]
final class GetSigninAccountController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[OA\Get(
        summary: 'Get signin account information',
        security: [['bearer' => []]],
        tags: ['Authorization'],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: HttpSpecification::STATUS_BAD_REQUEST),
            new OA\Response(
                response: Response::HTTP_OK,
                description: HttpSpecification::STATUS_OK,
                content: new OA\JsonContent(ref: new AD\Model(type: GetSigninAccountResponse::class)),
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: HttpSpecification::STATUS_UNAUTHORIZED),
        ],
    )]
    #[Route(
        path: '/auth/me',
        name: 'app_get_signin_account',
        methods: Request::METHOD_GET,
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function __invoke(#[MapMessage] GetSigninAccountRequest $message): Response
    {
        /** @var GetSigninAccountResponse $handledResult */
        $handledResult = $this->handle($message);

        return new JsonResponse(
            data: $this->normalizer->normalize($handledResult),
        );
    }
}
