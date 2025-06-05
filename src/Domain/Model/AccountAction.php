<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
enum AccountAction: string
{
    case Activate = 'activate';
    case Block = 'block';
    case Register = 'register';
    case Unblock = 'unblock';

    public function toString(): string
    {
        return $this->value;
    }
}
