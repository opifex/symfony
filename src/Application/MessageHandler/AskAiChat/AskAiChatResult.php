<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\AskAiChat;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class AskAiChatResult extends JsonResponse
{
    public static function success(string $message): self
    {
        return new self(
            data: [
                'response' => $message,
            ],
            status: Response::HTTP_OK,
        );
    }
}
