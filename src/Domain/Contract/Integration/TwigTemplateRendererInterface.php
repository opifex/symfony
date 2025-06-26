<?php

declare(strict_types=1);

namespace App\Domain\Contract\Integration;

use App\Domain\Exception\Integration\TwigTemplateRendererException;

interface TwigTemplateRendererInterface
{
    /**
     * @param array<string, mixed> $context
     * @throws TwigTemplateRendererException
     */
    public function render(string $name, array $context = []): string;
}
