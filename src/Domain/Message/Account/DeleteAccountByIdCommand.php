<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteAccountByIdCommand implements MessageInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Groups(MessageInterface::GROUP_URL)]
        public readonly string $uuid = '',
    ) {
    }
}
