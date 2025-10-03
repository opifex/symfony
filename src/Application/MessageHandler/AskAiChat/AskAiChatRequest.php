<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\AskAiChat;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
#[AsMessage]
final readonly class AskAiChatRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 1024)]
        public ?string $message = null,
        public ?string $sessionKey = null,
    ) {}
}
