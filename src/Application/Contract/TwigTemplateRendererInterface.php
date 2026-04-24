<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface TwigTemplateRendererInterface
{
    /**
     * @param array<array-key, mixed> $context
     */
    public function render(string $name, array $context = []): string;
}
