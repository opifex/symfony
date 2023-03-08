<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Message\MessageUuidTrait;

class DeleteAccountByIdCommand implements MessageInterface
{
    use MessageUuidTrait;
}
