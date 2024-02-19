<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\AccountActionInvalidException;

interface AccountStateMachineInterface
{
    /**
     * @throws AccountActionInvalidException
     */
    public function apply(string $uuid, string $action): void;
}
