<?php

declare(strict_types=1);

namespace App\Domain\Response\Auth;

use App\Domain\Response\AbstractAccountResponse;

final class GetSigninAccountInfoResponse extends AbstractAccountResponse
{
    public const GROUP_VIEW = __CLASS__ . ':view';
}
