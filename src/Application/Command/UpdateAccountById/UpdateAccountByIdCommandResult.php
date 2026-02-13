<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateAccountById;

use App\Domain\Foundation\HttpSpecification;
use App\Domain\Foundation\MessageHandlerResult;

final class UpdateAccountByIdCommandResult extends MessageHandlerResult
{
    public static function success(): self
    {
        return new self(
            status: HttpSpecification::HTTP_NO_CONTENT,
        );
    }
}
