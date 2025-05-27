<?php

declare(strict_types=1);

namespace App\Domain\Contract\Account;

use App\Domain\Exception\Account\AccountActionInvalidException;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Account;

interface AccountWorkflowManagerInterface
{
    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function activate(Account $account): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function block(Account $account): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function register(Account $account): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function unblock(Account $account): void;
}
