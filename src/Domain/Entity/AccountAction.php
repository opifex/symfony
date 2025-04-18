<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountAction
{
    public const string ACTIVATE = 'activate';
    public const string BLOCK = 'block';
    public const string REGISTER = 'register';
    public const string UNBLOCK = 'unblock';
}
