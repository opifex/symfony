<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\AskAiChat;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\AI\Platform\Result\TextResult;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class AskAiChatHandler
{
    public function __construct(
        private readonly RequestStack $requestStack,
        #[Autowire(service: 'ai.agent.wikipedia')]
        private readonly AgentInterface $agent,
    ) {
    }

    public function __invoke(AskAiChatRequest $request): AskAiChatResult
    {
        $sessionKey = $request->sessionKey;
        if (empty($sessionKey)) {
            $sessionKey = Uuid::v7()->toString();
        }

        $messages = new MessageBag();

        $messages->add(Message::ofUser($request->message));
        $result = $this->agent->call($messages);

        assert($result instanceof TextResult);

        $message = (string) $result->getContent();
        $messages->add(Message::ofAssistant($message));

        return AskAiChatResult::success($message);
    }
}
