<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Exception\Account\AccountActionInvalidException;
use App\Domain\Model\AccountAction;
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
    public function activate(string $id): void
    {
        $this->apply($id, action: AccountAction::ACTIVATE);
    }

    #[Override]
    public function block(string $id): void
    {
        $this->apply($id, action: AccountAction::BLOCK);
    }

    #[Override]
    public function register(string $id): void
    {
        $this->apply($id, action: AccountAction::REGISTER);
    }

    #[Override]
    public function unblock(string $id): void
    {
        $this->apply($id, action: AccountAction::UNBLOCK);
    }

    private function apply(string $id, string $action): void
    {
        $account = $this->accountEntityRepository->findOneById($id);

        if (!$this->accountStateMachine->can($account, $action)) {
            throw AccountActionInvalidException::create();
        }

        $this->accountStateMachine->apply($account, $action);
    }
}
