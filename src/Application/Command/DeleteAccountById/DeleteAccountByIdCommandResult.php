<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteAccountById;

use App\Domain\Foundation\AbstractHandlerResult;

final class DeleteAccountByIdCommandResult extends AbstractHandlerResult
{
    public static function success(): self
    {
        return new self();
    }
}
