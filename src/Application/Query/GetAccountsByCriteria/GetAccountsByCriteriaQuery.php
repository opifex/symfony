<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountsByCriteria;

use App\Domain\Account\AccountStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class GetAccountsByCriteriaQuery
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
