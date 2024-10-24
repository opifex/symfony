<?php

declare(strict_types=1);

namespace App\Application\Attribute;

use App\Application\Service\MessageValueResolver;
use Attribute;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[Exclude]
#[Attribute(Attribute::TARGET_PARAMETER)]
final class MapMessage extends ValueResolver
{
    public function __construct(string $resolver = MessageValueResolver::class)
    {
        parent::__construct($resolver);
    }
}
