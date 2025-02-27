<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UnblockAccountById;

use App\Domain\Contract\AccountStateMachineInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UnblockAccountByIdHandler
{
    public function __construct(
        private readonly AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(UnblockAccountByIdRequest $message): UnblockAccountByIdResponse
    {
        $this->accountStateMachine->unblock($message->uuid);

        return UnblockAccountByIdResponse::create();
    }
}
