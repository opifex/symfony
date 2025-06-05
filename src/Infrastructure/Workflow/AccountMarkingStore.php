<?php

declare(strict_types=1);

namespace App\Infrastructure\Workflow;

use App\Domain\Model\Account;
use App\Domain\Model\AccountStatus;
use Override;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

final class AccountMarkingStore implements MarkingStoreInterface
{
    #[Override]
    public function getMarking(object $subject): Marking
    {
        if (!$subject instanceof Account) {
            throw new InvalidArgumentException(message: 'Subject expected to be a valid account.');
        }

        return new Marking([$subject->getStatus()->toString() => 1]);
    }

    /**
     * @param array<string, mixed> $context
     */
    #[Override]
    public function setMarking(object $subject, Marking $marking, array $context = []): void
    {
        if (!$subject instanceof Account) {
            throw new InvalidArgumentException(message: 'Subject expected to be a valid account.');
        }

        $subject->updateStatus(AccountStatus::fromString((string) array_key_first($marking->getPlaces())));
    }
}
