<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Exception\AccountActionInvalidException;
use Override;
use Symfony\Component\Workflow\WorkflowInterface;

final class AccountStateMachine implements AccountStateMachineInterface
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private WorkflowInterface $accountStateMachine,
    ) {
    }

    #[Override]
    public function apply(string $uuid, string $action): void
    {
        $account = $this->accountRepository->findOneByUuid($uuid);

        if (!$this->accountStateMachine->can($account, $action)) {
            throw new AccountActionInvalidException(
                message: 'Provided action cannot be applied to account.',
            );
        }

        $this->accountStateMachine->apply($account, $action);
    }
}
