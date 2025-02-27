<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\BlockAccountById;

use App\Domain\Contract\AccountStateMachineInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BlockAccountByIdHandler
{
    public function __construct(
        private readonly AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(BlockAccountByIdRequest $message): BlockAccountByIdResponse
    {
        $this->accountStateMachine->block($message->uuid);

        return BlockAccountByIdResponse::create();
    }
}
