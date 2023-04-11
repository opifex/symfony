<?php

declare(strict_types=1);

namespace App\Domain\Notification\Account;

use App\Domain\Notification\AbstractNotification;

class AccountCreateNotification extends AbstractNotification
{
    public string $accountEmail = '';
}
