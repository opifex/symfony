<?php

declare(strict_types=1);

namespace App\Domain\Message\Auth;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SigninIntoAccountCommand implements MessageInterface
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        #[Groups(self::GROUP_BODY)]
        public readonly string $email = '',

        #[Assert\NotBlank]
        #[Groups(self::GROUP_BODY)]
        public readonly string $password = '',
    ) {
    }
}
