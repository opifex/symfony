<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use App\Domain\Exception\AccountActionInvalidException;
use Override;
use Symfony\Component\Workflow\WorkflowInterface;

final class AccountStateMachine implements AccountStateMachineInterface
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
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
        $account = $this->accountRepository->findOneByUuid($uuid);

        if (!$this->accountStateMachine->can($account, $action)) {
            throw AccountActionInvalidException::create();
        }

        $this->accountStateMachine->apply($account, $action);
    }
}
