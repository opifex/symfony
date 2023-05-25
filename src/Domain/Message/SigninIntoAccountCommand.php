<?php

declare(strict_types=1);

namespace App\Domain\Message;

use App\Domain\Contract\MessageInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class SigninIntoAccountCommand implements MessageInterface
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public readonly string $email = '',

        #[Assert\NotBlank]
        public readonly string $password = '',
    ) {
    }
}
