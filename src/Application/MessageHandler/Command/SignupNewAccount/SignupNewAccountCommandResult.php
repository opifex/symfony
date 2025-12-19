<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\SignupNewAccount;

use App\Domain\Foundation\HttpSpecification;
use App\Domain\Foundation\MessageHandlerResult;

final class SignupNewAccountCommandResult extends MessageHandlerResult
{
    public static function success(): self
    {
        return new self(
            status: HttpSpecification::HTTP_NO_CONTENT,
        );
    }
}
