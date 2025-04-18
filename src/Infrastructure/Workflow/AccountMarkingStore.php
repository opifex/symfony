<?php

declare(strict_types=1);

namespace App\Infrastructure\Workflow;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
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
        $accountUuid = $this->getSubjectIdentifier($subject);
        $markingStatus = $this->accountRepository->findStatusByUuid($accountUuid);

        return new Marking([$markingStatus => 1]);
    }

    /**
     * @param array<string, mixed> $context
     */
    #[Override]
    public function setMarking(object $subject, Marking $marking, array $context = []): void
    {
        $accountUuid = $this->getSubjectIdentifier($subject);
        $markingStatus = (string) array_key_first($marking->getPlaces());
        $this->accountRepository->updateStatusByUuid($accountUuid, $markingStatus);
    }

    private function getSubjectIdentifier(object $subject): string
    {
        if (!$subject instanceof Account) {
            throw new InvalidArgumentException(message: 'Subject expected to be a valid account.');
        }

        return $subject->getUuid();
    }
}
