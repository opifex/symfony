<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Contract\TemplateRendererInterface;
use App\Domain\Exception\TemplateRendererException;
use Override;
use Twig\Environment;
use Twig\Error\Error;

final class SensiolabsTwigAdapter implements TemplateRendererInterface
{
    public function __construct(private readonly Environment $environment)
    {
    }

    #[Override]
    public function render(string $name, array $context = []): string
    {
        try {
            return $this->environment->render($name, $context);
        } catch (Error $e) {
            throw TemplateRendererException::fromException($e);
        }
    }
}
