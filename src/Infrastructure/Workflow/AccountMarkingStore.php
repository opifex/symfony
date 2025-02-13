<?php

declare(strict_types=1);

namespace App\Infrastructure\Workflow;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountStatus;
use Override;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

final class AccountMarkingStore implements MarkingStoreInterface
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {
    }

    #[Override]
    public function getMarking(object $subject): Marking
    {
        if (!$subject instanceof Account) {
            throw new InvalidArgumentException(message: 'Subject expected to be a valid account.');
        }

        $account = $this->accountRepository->findOneByUuid($subject->getUuid());

        return new Marking([$account->getStatus()->value => 1]);
    }

    /**
     * @param object $subject
     * @param Marking $marking
     * @param array<string, mixed> $context
     */
    #[Override]
    public function setMarking(object $subject, Marking $marking, array $context = []): void
    {
        if (!$subject instanceof Account) {
            throw new InvalidArgumentException(message: 'Subject expected to be a valid account.');
        }

        $status = AccountStatus::fromValue((string) array_key_first($marking->getPlaces()));
        $this->accountRepository->updateStatusByUuid($subject->getUuid(), $status);
    }
}
