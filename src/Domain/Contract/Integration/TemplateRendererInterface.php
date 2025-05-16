<?php

declare(strict_types=1);

namespace App\Domain\Contract\Integration;

use App\Domain\Exception\Integration\TemplateRendererException;

interface TemplateRendererInterface
{
    /**
     * @param array<string, mixed> $context
     * @throws TemplateRendererException
     */
    public function render(string $name, array $context = []): string;
}
