<?php

declare(strict_types=1);

namespace App\Domain\Message\Auth;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SignupNewAccountCommand implements MessageInterface
{
    #[Assert\Email]
    #[Assert\NotBlank]
    #[Groups(self::GROUP_BODY)]
    public string $email = '';

    #[Assert\Length(min: 8, max: 32)]
    #[Assert\NotBlank]
    #[Assert\NotCompromisedPassword]
    #[Groups(self::GROUP_BODY)]
    public string $password = '';
}
