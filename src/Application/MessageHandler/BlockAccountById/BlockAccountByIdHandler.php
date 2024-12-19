<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\BlockAccountById;

use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BlockAccountByIdHandler
{
    public function __construct(private readonly AccountStateMachineInterface $accountStateMachine)
    {
    }

    public function __invoke(BlockAccountByIdRequest $message): BlockAccountByIdResponse
    {
        $this->accountStateMachine->apply($message->uuid, action: AccountAction::Block);

        return new BlockAccountByIdResponse();
    }
}
