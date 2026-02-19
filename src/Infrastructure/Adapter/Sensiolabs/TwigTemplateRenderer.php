<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Sensiolabs;

use App\Application\Contract\TwigTemplateRendererInterface;
use App\Infrastructure\Adapter\Sensiolabs\Exception\RenderingFailedException;
use Override;
use Twig\Environment;
use Twig\Error\Error;

final class TwigTemplateRenderer implements TwigTemplateRendererInterface
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
        } catch (Error $exception) {
            throw RenderingFailedException::fromException($exception);
        }
    }
}
