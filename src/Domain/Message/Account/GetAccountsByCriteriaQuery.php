<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class GetAccountsByCriteriaQuery implements MessageInterface
{
    /**
     * @param string[] $criteria
     * @param string[] $sort
     */
    public function __construct(
        #[Assert\DivisibleBy(value: 1)]
        #[Assert\Positive]
        #[Groups(self::URL_PARAM)]
        public readonly int $limit = 10,

        #[Assert\DivisibleBy(value: 1)]
        #[Assert\PositiveOrZero]
        #[Groups(self::URL_PARAM)]
        public readonly int $offset = 0,

        #[Assert\Collection(
            fields: [
                'email' => new Assert\Type(type: ['string', 'int', 'float']),
                'status' => new Assert\Choice(choices: AccountStatus::LIST),
            ],
            allowMissingFields: true,
        )]
        public readonly array $criteria = [],

        #[Assert\Collection(
            fields: [
                'created_at' => new Assert\Choice(choices: ['asc', 'desc']),
                'email' => new Assert\Choice(choices: ['asc', 'desc']),
                'status' => new Assert\Choice(choices: ['asc', 'desc']),
                'updated_at' => new Assert\Choice(choices: ['asc', 'desc']),
            ],
            allowMissingFields: true,
        )]
        public readonly array $sort = [],
    ) {
    }
}
