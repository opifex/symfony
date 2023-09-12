<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountsByCriteria;

use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\AccountStatus;
use App\Domain\Entity\SortingOrder;
use Symfony\Component\Validator\Constraints as Assert;

final class GetAccountsByCriteriaQuery
{
    public function __construct(
        #[Assert\Length(max: 320)]
        public readonly ?string $email = null,

        #[Assert\Choice(choices: AccountStatus::STATUSES)]
        public readonly ?string $status = null,

        #[Assert\Choice(choices: AccountSearchCriteria::SORTING_FIELDS)]
        public readonly string $sort = AccountSearchCriteria::FIELD_CREATED_AT,

        #[Assert\Choice(choices: SortingOrder::SORTING)]
        public readonly string $order = SortingOrder::DESC,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\Positive]
        public readonly int $limit = 10,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\PositiveOrZero]
        public readonly int $offset = 0,
    ) {
    }
}
