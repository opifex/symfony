<?php

declare(strict_types=1);

namespace App\Application\Command\SignupNewAccount;

use App\Domain\Foundation\MessageHandlerResult;

final class SignupNewAccountCommandResult extends MessageHandlerResult
{
    public static function success(): self
    {
        return new self();
    }
}
