<?php

declare(strict_types=1);

namespace App\Domain\Contract\Account;

use App\Domain\Exception\Account\AccountInvalidActionException;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Account;

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
