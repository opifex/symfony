<?php

declare(strict_types=1);

namespace App\Application\Handler\ApplyAccountAction;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountActionInvalidException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class ApplyAccountActionHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private WorkflowInterface $accountStateMachine,
    ) {
    }

    public function __invoke(ApplyAccountActionCommand $message): ApplyAccountActionResponse
    {
        $account = $this->accountRepository->findOneByUuid($message->uuid);

        if (!$this->accountStateMachine->can($account, $message->action)) {
            throw new AccountActionInvalidException(
                message: 'Provided action cannot be applied to account.',
            );
        }

        $this->accountStateMachine->apply($account, $message->action);

        return new ApplyAccountActionResponse();
    }
}
