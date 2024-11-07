<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

#[Exclude]
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
