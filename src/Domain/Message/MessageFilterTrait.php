<?php

declare(strict_types=1);

namespace App\Domain\Message;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait MessageFilterTrait
{
    #[Assert\DivisibleBy(value: 1)]
    #[Assert\Positive]
    #[Groups(MessageInterface::GROUP_URL)]
    public int $limit = 10;

    #[Assert\DivisibleBy(value: 1)]
    #[Assert\PositiveOrZero]
    #[Groups(MessageInterface::GROUP_URL)]
    public int $offset = 0;
}
