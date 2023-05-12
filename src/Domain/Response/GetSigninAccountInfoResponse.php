<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Response\Account\AccountResponseItem;

final class GetSigninAccountInfoResponse extends AccountResponseItem
{
    public const GROUP_VIEW = __CLASS__ . ':view';
}
