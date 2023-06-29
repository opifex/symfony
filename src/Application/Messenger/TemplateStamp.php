<?php

declare(strict_types=1);

namespace App\Application\Messenger;

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

final class TemplateStamp implements NonSendableStampInterface
{
    public function __construct(private string $template)
    {
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
