<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Message\MessageFilterTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class GetAccountsByCriteriaQuery implements MessageInterface
{
    use MessageFilterTrait;

    #[Assert\Length(min: 0, max: 320)]
    #[Groups(self::GROUP_URL)]
    public string $email = '';
}
