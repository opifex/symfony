<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Contract\TemplateEngineInterface;
use App\Domain\Exception\TemplateEngineException;
use Override;
use Twig\Environment;
use Twig\Error\Error;

final class SensiolabsTwigAdapter implements TemplateEngineInterface
{
    public function __construct(private Environment $environment)
    {
    }

    #[Override]
    public function render(string $name, array $context = []): string
    {
        try {
            return $this->environment->render($name, $context);
        } catch (Error $e) {
            throw new TemplateEngineException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
