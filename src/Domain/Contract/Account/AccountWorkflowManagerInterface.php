<?php

declare(strict_types=1);

namespace App\Domain\Contract\Account;

use App\Domain\Exception\Account\AccountActionInvalidException;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\AccountIdentifier;

interface AccountWorkflowManagerInterface
{
    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function activate(AccountIdentifier $id): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function block(AccountIdentifier $id): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function register(AccountIdentifier $id): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function unblock(AccountIdentifier $id): void;
}
