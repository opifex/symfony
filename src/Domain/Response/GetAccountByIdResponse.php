<?php

declare(strict_types=1);

namespace App\Domain\Response;

final class GetAccountByIdResponse extends AbstractAccountResponse
{
    public const GROUP_VIEW = __CLASS__ . ':view';
}
