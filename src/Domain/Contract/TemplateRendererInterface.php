<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\TemplateRendererException;

interface TemplateRendererInterface
{
    /**
     * @param string $name
     * @param array&array<string, mixed> $context
     * @throws TemplateRendererException
     */
    public function render(string $name, array $context = []): string;
}
