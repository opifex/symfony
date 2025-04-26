<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountEntityRepositoryInterface;
use App\Domain\Contract\AccountWorkflowManagerInterface;
use App\Domain\Exception\AccountActionInvalidException;
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
    public function activate(string $uuid): void
    {
        $this->apply($uuid, action: AccountAction::ACTIVATE);
    }

    #[Override]
    public function block(string $uuid): void
    {
        $this->apply($uuid, action: AccountAction::BLOCK);
    }

    #[Override]
    public function register(string $uuid): void
    {
        $this->apply($uuid, action: AccountAction::REGISTER);
    }

    #[Override]
    public function unblock(string $uuid): void
    {
        $this->apply($uuid, action: AccountAction::UNBLOCK);
    }

    private function apply(string $uuid, string $action): void
    {
        $account = $this->accountEntityRepository->findOneByUuid($uuid);

        if (!$this->accountStateMachine->can($account, $action)) {
            throw AccountActionInvalidException::create();
        }

        $this->accountStateMachine->apply($account, $action);
    }
}
