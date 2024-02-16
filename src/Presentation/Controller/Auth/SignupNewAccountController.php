<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Auth;

use App\Application\Attribute\MapMessage;
use App\Application\Handler\SignupNewAccount\SignupNewAccountCommand;
use App\Application\Handler\SignupNewAccount\SignupNewAccountResponse;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
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
final class SignupNewAccountController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[OA\Post(
        summary: 'Signup new account',
        requestBody: new OA\RequestBody(
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
        path: '/auth/signup',
        name: 'app_signup_new_account',
        methods: Request::METHOD_POST,
    )]
    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    public function __invoke(#[MapMessage] SignupNewAccountCommand $message): Response
    {
        /** @var SignupNewAccountResponse $handledResult */
        $handledResult = $this->handle($message);

        return new JsonResponse(
            data: $this->normalizer->normalize($handledResult),
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
