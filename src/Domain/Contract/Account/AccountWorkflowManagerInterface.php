<?php

declare(strict_types=1);

namespace App\Domain\Contract\Account;

use App\Domain\Exception\Account\AccountActionInvalidException;
use App\Domain\Exception\Account\AccountNotFoundException;

interface AccountWorkflowManagerInterface
{
    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function activate(string $id): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function block(string $id): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function register(string $id): void;

    /**
     * @throws AccountActionInvalidException
     * @throws AccountNotFoundException
     */
    public function unblock(string $id): void;
}
