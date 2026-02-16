<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateAccountById;

use App\Domain\Foundation\AbstractHandlerResult;

final class UpdateAccountByIdCommandResult extends AbstractHandlerResult
{
    public static function success(): self
    {
        return new self();
    }
}
