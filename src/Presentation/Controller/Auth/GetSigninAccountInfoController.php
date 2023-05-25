<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Domain\Message\GetSigninAccountInfoQuery;
use App\Domain\Response\GetSigninAccountInfoResponse;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[AsController]
final class GetSigninAccountInfoController extends AbstractController
{
    #[OA\Get(
        summary: 'Get signin account information',
        security: [['bearer' => []]],
        tags: ['Authorization'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(
                    ref: new Model(
                        type: GetSigninAccountInfoResponse::class,
                        groups: [GetSigninAccountInfoResponse::GROUP_VIEW],
                    ),
                ),
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ],
    )]
    #[Route(
        path: '/api/auth/me',
        name: __CLASS__,
        methods: Request::METHOD_GET,
        format: 'json',
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED, message: 'Not privileged to request the resource.')]
    public function __invoke(GetSigninAccountInfoQuery $message): Envelope
    {
        return $this->queryBus->dispatch($message)->with(
            new SerializerStamp([
                AbstractNormalizer::GROUPS => [
                    GetSigninAccountInfoResponse::GROUP_VIEW,
                ],
            ]),
        );
    }
}
