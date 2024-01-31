<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\TemplateEngineException;

interface TemplateEngineInterface
{
    /**
     * @param string $name
     * @param array&array<string, mixed> $context
     * @throws TemplateEngineException
     */
    public function render(string $name, array $context = []): string;
}
