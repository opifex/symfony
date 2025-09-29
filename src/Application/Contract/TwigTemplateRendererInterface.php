<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Exception\TwigRenderingFailedException;

interface TwigTemplateRendererInterface
{
    /**
     * @param array<string, mixed> $context
     * @throws TwigRenderingFailedException
     */
    public function render(string $name, array $context = []): string;
}
