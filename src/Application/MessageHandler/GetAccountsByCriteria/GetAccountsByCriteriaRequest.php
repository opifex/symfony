<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Model\AccountStatus;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
#[AsMessage]
final class GetAccountsByCriteriaRequest
{
    public function __construct(
        #[Assert\Length(max: 320)]
        public readonly ?string $email = null,

        #[Assert\Choice(callback: [AccountStatus::class, 'values'])]
        public readonly ?string $status = null,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\Positive]
        public readonly int $page = 1,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\Positive]
        public readonly int $limit = 10,
    ) {
    }
}
