<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface TwigTemplateRendererInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function render(string $name, array $context = []): string;
}
