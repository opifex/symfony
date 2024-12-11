<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\ActivateAccountById;

use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ActivateAccountByIdHandler
{
    public function __construct(private AccountStateMachineInterface $accountStateMachine)
    {
    }

    public function __invoke(ActivateAccountByIdRequest $message): ActivateAccountByIdResponse
    {
        $this->accountStateMachine->apply($message->uuid, action: AccountAction::Activate);

        return new ActivateAccountByIdResponse();
    }
}
