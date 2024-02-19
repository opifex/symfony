<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\AccountActionInvalidException;
use App\Domain\Exception\AccountNotFoundException;

interface AccountStateMachineInterface
{
    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function apply(string $uuid, string $action): void;
}
