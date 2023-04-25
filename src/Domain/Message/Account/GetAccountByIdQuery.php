<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class GetAccountByIdQuery implements MessageInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Groups(self::GROUP_URL)]
        public readonly string $uuid = '',
    ) {
    }
}
