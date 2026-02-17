<?php

declare(strict_types=1);

namespace App\Application\Command\BlockAccountById;

use JsonSerializable;
use Override;

final class BlockAccountByIdCommandResult implements JsonSerializable
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
