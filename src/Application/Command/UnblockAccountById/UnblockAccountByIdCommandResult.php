<?php

declare(strict_types=1);

namespace App\Application\Command\UnblockAccountById;

use App\Domain\Foundation\AbstractHandlerResult;

final class UnblockAccountByIdCommandResult extends AbstractHandlerResult
{
    public static function success(): self
    {
        return new self();
    }
}
