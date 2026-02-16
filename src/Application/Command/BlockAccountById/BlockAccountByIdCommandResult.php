<?php

declare(strict_types=1);

namespace App\Application\Command\BlockAccountById;

use App\Domain\Foundation\AbstractHandlerResult;

final class BlockAccountByIdCommandResult extends AbstractHandlerResult
{
    public static function success(): self
    {
        return new self();
    }
}
