<?php

declare(strict_types=1);

namespace App\Domain\Account\Contract;

use App\Domain\Account\Account;
use App\Domain\Account\Exception\AccountInvalidActionException;

interface AccountStateMachineInterface
{
    /**
     * @throws AccountInvalidActionException
     */
    public function activate(Account $account): void;

    /**
     * @throws AccountInvalidActionException
     */
    public function block(Account $account): void;

    /**
     * @throws AccountInvalidActionException
     */
    public function register(Account $account): void;

    /**
     * @throws AccountInvalidActionException
     */
    public function unblock(Account $account): void;
}
