<?php

declare(strict_types=1);

namespace App\Domain\Entity\Account;

enum AccountAction: string
{
    case BLOCK = 'block';

    case UNBLOCK = 'unblock';

    case VERIFY = 'verify';
}
