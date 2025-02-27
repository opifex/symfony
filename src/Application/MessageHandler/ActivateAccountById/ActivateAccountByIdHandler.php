<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\ActivateAccountById;

use App\Domain\Contract\AccountStateMachineInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ActivateAccountByIdHandler
{
    public function __construct(
        private readonly AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(ActivateAccountByIdRequest $message): ActivateAccountByIdResponse
    {
        $this->accountStateMachine->activate($message->uuid);

        return ActivateAccountByIdResponse::create();
    }
}
