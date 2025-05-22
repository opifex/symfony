<?php

declare(strict_types=1);

namespace App\Domain\Contract\Account;

use App\Domain\Exception\Account\AccountActionInvalidException;
use App\Domain\Model\Account;

interface AccountWorkflowManagerInterface
{
    /**
     * @throws AccountActionInvalidException
     */
    public function activate(Account $account): void;

    /**
     * @throws AccountActionInvalidException
     */
    public function block(Account $account): void;

    /**
     * @throws AccountActionInvalidException
     */
    public function register(Account $account): void;

    /**
     * @throws AccountActionInvalidException
     */
    public function unblock(Account $account): void;
}
