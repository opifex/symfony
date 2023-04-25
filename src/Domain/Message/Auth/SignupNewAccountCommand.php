<?php

declare(strict_types=1);

namespace App\Domain\Message\Auth;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class SignupNewAccountCommand implements MessageInterface
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        #[Groups(self::BODY_PARAM)]
        public readonly string $email = '',

        #[Assert\Length(min: 8, max: 32)]
        #[Assert\NotBlank]
        #[Assert\NotCompromisedPassword]
        #[Groups(self::BODY_PARAM)]
        public readonly string $password = '',
    ) {
    }
}
