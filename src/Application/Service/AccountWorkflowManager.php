<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Exception\Account\AccountActionInvalidException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountAction;
use Override;
use Symfony\Component\Workflow\WorkflowInterface;

final class AccountWorkflowManager implements AccountWorkflowManagerInterface
{
    public function __construct(
        private readonly WorkflowInterface $accountStateMachine,
    ) {
    }

    #[Override]
    public function activate(Account $account): void
    {
        $this->apply($account, action: AccountAction::ACTIVATE);
    }

    #[Override]
    public function block(Account $account): void
    {
        $this->apply($account, action: AccountAction::BLOCK);
    }

    #[Override]
    public function register(Account $account): void
    {
        $this->apply($account, action: AccountAction::REGISTER);
    }

    #[Override]
    public function unblock(Account $account): void
    {
        $this->apply($account, action: AccountAction::UNBLOCK);
    }

    private function apply(Account $account, string $action): void
    {
        if (!$this->accountStateMachine->can($account, $action)) {
            throw AccountActionInvalidException::create();
        }

        $this->accountStateMachine->apply($account, $action);
    }
}
