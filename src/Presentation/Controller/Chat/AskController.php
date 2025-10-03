<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Chat;

use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\MessageHandler\AskAiChat\AskAiChatRequest;

#[AsController]
final class AskController extends AbstractController
{
    #[Route(
        path: "/chat/ask",
        name: "ai_chat_ask",
        methods: Request::METHOD_POST,
    )]
    public function __invoke(#[ValueResolver("payload")] AskAiChatRequest $request): Response
    {
        return $this->getHandledResult($request);
    }
}
