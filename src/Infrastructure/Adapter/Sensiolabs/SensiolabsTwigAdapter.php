<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Sensiolabs;

use App\Domain\Contract\Integration\TwigTemplateRendererInterface;
use App\Domain\Exception\Integration\TwigTemplateRendererException;
use Override;
use Twig\Environment;
use Twig\Error\Error;

final class SensiolabsTwigAdapter implements TwigTemplateRendererInterface
{
    public function __construct(
        private readonly Environment $environment,
    ) {
    }

    #[Override]
    public function render(string $name, array $context = []): string
    {
        try {
            return $this->environment->render($name, $context);
        } catch (Error $e) {
            throw TwigTemplateRendererException::fromException($e);
        }
    }
}
