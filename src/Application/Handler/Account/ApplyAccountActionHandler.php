<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Exception\AccountNotFoundException;
use App\Domain\Message\Account\ApplyAccountActionCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class ApplyAccountActionHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private WorkflowInterface $accountStateMachine,
    ) {
    }

    public function __invoke(ApplyAccountActionCommand $message): void
    {
        try {
            $account = $this->accountRepository->findOneByUuid($message->uuid);
        } catch (AccountNotFoundException $e) {
            throw new NotFoundHttpException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }

        if (!$this->accountStateMachine->can($account, $message->action)) {
            throw new BadRequestHttpException(
                message: 'Provided action cannot be applied to account.',
            );
        }

        $this->accountStateMachine->apply($account, $message->action);
    }
}
