<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class GetAccountsByCriteriaQuery implements MessageInterface
{
    #[Assert\DivisibleBy(value: 1)]
    #[Assert\Positive]
    #[Groups(MessageInterface::GROUP_URL)]
    public int $limit = 10;

    #[Assert\DivisibleBy(value: 1)]
    #[Assert\PositiveOrZero]
    #[Groups(MessageInterface::GROUP_URL)]
    public int $offset = 0;

    /**
     * @var string[]
     */
    #[Assert\Collection(
        fields: [
            'email' => new Assert\Type(type: ['string', 'int', 'float']),
            'status' => new Assert\Choice(choices: AccountStatus::LIST),
        ],
        allowMissingFields: true,
    )]
    public array $criteria = [];

    /**
     * @var string[]
     */
    #[Assert\Collection(
        fields: [
            'created_at' => new Assert\Choice(choices: ['asc', 'desc']),
            'email' => new Assert\Choice(choices: ['asc', 'desc']),
            'status' => new Assert\Choice(choices: ['asc', 'desc']),
            'updated_at' => new Assert\Choice(choices: ['asc', 'desc']),
        ],
        allowMissingFields: true,
    )]
    public array $sort = [];
}
