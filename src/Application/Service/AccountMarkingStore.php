<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use Override;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

final class AccountMarkingStore implements MarkingStoreInterface
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    #[Override]
    public function getMarking(object $subject): Marking
    {
        if (!$subject instanceof Account) {
            throw new LogicException(message: 'Subject expected to be a valid account.');
        }

        return new Marking([$subject->getStatus() => 1]);
    }

    /**
     * @param object $subject
     * @param Marking $marking
     * @param array&array<string, mixed> $context
     */
    #[Override]
    public function setMarking(object $subject, Marking $marking, array $context = []): void
    {
        if (!$subject instanceof Account) {
            throw new LogicException(message: 'Subject expected to be a valid account.');
        }

        $status = (string) key($marking->getPlaces());

        $this->accountRepository->updateStatusByUuid($subject->getUuid(), $status);
    }
}
