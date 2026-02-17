<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateAccountById;

use JsonSerializable;
use Override;

final class UpdateAccountByIdCommandResult implements JsonSerializable
{
    public static function success(): self
    {
        return new self();
    }

    #[Override]
    public function jsonSerialize(): null
    {
        return null;
    }
}
