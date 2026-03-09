<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountsByCriteria;

use App\Domain\Account\AccountStatus;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetAccountsByCriteriaQuery
{
    public function __construct(
        #[Assert\Length(max: 320)]
        public ?string $email = null,

        #[Assert\Choice(callback: [AccountStatus::class, 'values'])]
        public ?string $status = null,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\Positive]
        public int $page = 1,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\Positive]
        public int $limit = 10,
    ) {
    }
}
