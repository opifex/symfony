<?php

declare(strict_types=1);

namespace App\Infrastructure\Workflow;

use App\Domain\Account\Account;
use App\Domain\Account\AccountAction;
use App\Domain\Account\AccountStatus;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use App\Domain\Account\Exception\AccountInvalidActionException;
use NoDiscard;
use Override;
use Symfony\Component\Workflow\WorkflowInterface;

final class AccountStateMachine implements AccountStateMachineInterface
{
    public function __construct(
        private readonly WorkflowInterface $accountStateMachine,
    ) {
    }

    #[NoDiscard]
    #[Override]
    public function activate(Account $account): Account
    {
        return $this->applyTransition($account, action: AccountAction::Activate);
    }

    #[NoDiscard]
    #[Override]
    public function block(Account $account): Account
    {
        return $this->applyTransition($account, action: AccountAction::Block);
    }

    #[NoDiscard]
    #[Override]
    public function register(Account $account): Account
    {
        return $this->applyTransition($account, action: AccountAction::Register);
    }

    #[NoDiscard]
    #[Override]
    public function unblock(Account $account): Account
    {
        return $this->applyTransition($account, action: AccountAction::Unblock);
    }

    private function applyTransition(Account $account, AccountAction $action): Account
    {
        $marking = new AccountMarking($account->status->toString());

        if (!$this->accountStateMachine->can($marking, $action->toString())) {
            throw AccountInvalidActionException::create();
        }

        $this->accountStateMachine->apply($marking, $action->toString());

        return $account->withStatus(AccountStatus::fromString($marking->marking));
    }
}
