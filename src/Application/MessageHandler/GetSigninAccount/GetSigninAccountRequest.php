<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetSigninAccount;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[Exclude]
#[AsMessage]
final class GetSigninAccountRequest
{
}
