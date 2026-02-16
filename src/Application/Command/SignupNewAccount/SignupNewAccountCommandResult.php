<?php

declare(strict_types=1);

namespace App\Application\Command\SignupNewAccount;

use App\Domain\Foundation\AbstractHandlerResult;

final class SignupNewAccountCommandResult extends AbstractHandlerResult
{
    public static function success(): self
    {
        return new self();
    }
}
