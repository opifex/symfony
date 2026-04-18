<?php

declare(strict_types=1);

namespace App\Application\Command\SignoutFromAccount;

use JsonSerializable;
use Override;

final readonly class SignoutFromAccountCommandResult implements JsonSerializable
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
