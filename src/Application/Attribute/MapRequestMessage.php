<?php

declare(strict_types=1);

namespace App\Application\Attribute;

use App\Application\Service\RequestMessageValueResolver;
use Attribute;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class MapRequestMessage extends ValueResolver
{
    public function __construct(string $resolver = RequestMessageValueResolver::class)
    {
        parent::__construct($resolver);
    }
}
