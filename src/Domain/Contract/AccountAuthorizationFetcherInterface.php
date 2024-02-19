<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\Account;
use App\Domain\Entity\AuthorizationToken;
use App\Domain\Exception\AccountUnauthorizedException;

interface AccountAuthorizationFetcherInterface
{
    /**
     * @throws AccountUnauthorizedException
     */
    public function fetchAccount(): Account;

    /**
     * @throws AccountUnauthorizedException
     */
    public function fetchToken(): AuthorizationToken;
}
