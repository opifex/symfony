<?php

declare(strict_types=1);

namespace App\Domain\Message\Auth;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class SigninIntoAccountCommand implements MessageInterface
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        #[Groups(self::BODY_PARAM)]
        public readonly string $email = '',

        #[Assert\NotBlank]
        #[Groups(self::BODY_PARAM)]
        public readonly string $password = '',
    ) {
    }
}
