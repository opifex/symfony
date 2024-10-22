<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\Account;
use App\Domain\Exception\AccountUnauthorizedException;

interface AccountTokenStorageInterface
{
    /**
     * @throws AccountUnauthorizedException
     */
    public function getAccount(): Account;
}
