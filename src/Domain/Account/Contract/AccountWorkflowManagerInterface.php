<?php

declare(strict_types=1);

namespace App\Domain\Account\Contract;

use App\Domain\Account\Account;
use App\Domain\Account\Exception\AccountInvalidActionException;
use App\Domain\Account\Exception\AccountNotFoundException;

interface AccountWorkflowManagerInterface
{
    /**
     * @throws AccountInvalidActionException
     * @throws AccountNotFoundException
     */
    public function activate(Account $account): void;

    /**
     * @throws AccountInvalidActionException
     * @throws AccountNotFoundException
     */
    public function block(Account $account): void;

    /**
     * @throws AccountInvalidActionException
     * @throws AccountNotFoundException
     */
    public function register(Account $account): void;

    /**
     * @throws AccountInvalidActionException
     * @throws AccountNotFoundException
     */
    public function unblock(Account $account): void;
}
