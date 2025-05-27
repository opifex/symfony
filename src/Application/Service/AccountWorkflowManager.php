<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Exception\Account\AccountActionInvalidException;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountAction;
use App\Domain\Model\AccountIdentifier;
use Override;
use Symfony\Component\Workflow\WorkflowInterface;

final class AccountWorkflowManager implements AccountWorkflowManagerInterface
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly WorkflowInterface $accountStateMachine,
    ) {
    }

    #[Override]
    public function activate(AccountIdentifier $id): void
    {
        $this->apply($id, action: AccountAction::ACTIVATE);
    }

    #[Override]
    public function block(AccountIdentifier $id): void
    {
        $this->apply($id, action: AccountAction::BLOCK);
    }

    #[Override]
    public function register(AccountIdentifier $id): void
    {
        $this->apply($id, action: AccountAction::REGISTER);
    }

    #[Override]
    public function unblock(AccountIdentifier $id): void
    {
        $this->apply($id, action: AccountAction::UNBLOCK);
    }

    private function apply(AccountIdentifier $id, string $action): void
    {
        $account = $this->accountEntityRepository->findOneByid($id);

        if (!$account instanceof Account) {
            throw AccountNotFoundException::create();
        }

        if (!$this->accountStateMachine->can($account, $action)) {
            throw AccountActionInvalidException::create();
        }

        $this->accountStateMachine->apply($account, $action);
        $this->accountEntityRepository->save($account);
    }
}
