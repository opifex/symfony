<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteAccountById;

use App\Domain\Foundation\MessageHandlerResult;

final class DeleteAccountByIdCommandResult extends MessageHandlerResult
{
    public static function success(): self
    {
        return new self();
    }
}
