<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class GetAccountsByCriteriaQuery implements MessageInterface
{
    public function __construct(
        public readonly ?string $email = null,

        #[Assert\Choice(choices: AccountStatus::LIST)]
        public readonly ?string $status = null,

        #[Assert\Choice(choices: ['created_at', 'email', 'status', 'updated_at'])]
        public readonly ?string $sort = null,

        #[Assert\Choice(choices: ['asc', 'desc'])]
        public readonly ?string $order = null,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\Positive]
        public readonly int $limit = 10,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\PositiveOrZero]
        public readonly int $offset = 0,
    ) {
    }
}
