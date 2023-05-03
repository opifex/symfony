<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Response\Account\AccountResponseItem;

final class GetAccountByIdResponse extends AccountResponseItem
{
    final public const GROUP_VIEW = __CLASS__ . ':view';
}
