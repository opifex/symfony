<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\ApplyAccountAction;

use App\Domain\Contract\AccountStateMachineInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ApplyAccountActionHandler
{
    public function __construct(private AccountStateMachineInterface $accountStateMachine)
    {
    }

    public function __invoke(ApplyAccountActionRequest $message): ApplyAccountActionResponse
    {
        $this->accountStateMachine->apply($message->uuid, $message->action);

        return new ApplyAccountActionResponse();
    }
}
