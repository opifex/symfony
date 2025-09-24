<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Exception\Account\AccountInvalidActionException;
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
        $this->apply($account, action: AccountAction::Activate);
    }

    #[Override]
    public function block(Account $account): void
    {
        $this->apply($account, action: AccountAction::Block);
    }

    #[Override]
    public function register(Account $account): void
    {
        $this->apply($account, action: AccountAction::Register);
    }

    #[Override]
    public function unblock(Account $account): void
    {
        $this->apply($account, action: AccountAction::Unblock);
    }

    private function apply(Account $account, AccountAction $action): void
    {
        if (!$this->accountStateMachine->can($account, $action->toString())) {
            throw AccountInvalidActionException::create();
        }

        $this->accountStateMachine->apply($account, $action->toString());
    }
}
