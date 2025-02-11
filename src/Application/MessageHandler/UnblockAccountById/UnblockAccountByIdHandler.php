<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UnblockAccountById;

use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UnblockAccountByIdHandler
{
    public function __construct(private readonly AccountStateMachineInterface $accountStateMachine)
    {
    }

    public function __invoke(UnblockAccountByIdRequest $message): UnblockAccountByIdResponse
    {
        $this->accountStateMachine->apply($message->uuid, action: AccountAction::Unblock);

        return UnblockAccountByIdResponse::create();
    }
}
