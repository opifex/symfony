<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use App\Domain\Contract\TemplateRendererInterface;
use App\Domain\Exception\TemplateRendererException;
use Override;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\RuntimeException;

final class HtmlTemplateEncoder implements EncoderInterface
{
    public const string FORMAT = 'html';
    public const string TEMPLATE = 'template';

    public function __construct(private TemplateRendererInterface $templateRenderer)
    {
    }

    #[Override]
    public function encode(mixed $data, string $format, array $context = []): string
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException(message: 'Data expected to be a valid array.');
        }

        if (!is_string(value: $context[self::TEMPLATE] ?? null)) {
            throw new InvalidArgumentException(message: 'Template expected to be a valid string.');
        }

        try {
            return $this->templateRenderer->render($context[self::TEMPLATE], $data);
        } catch (TemplateRendererException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    #[Override]
    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
