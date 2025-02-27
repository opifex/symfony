<?php

namespace Tests\Support\Extension;

use Codeception\Events;
use Codeception\Extension;

final class DisableCollector extends Extension
{
    /**
     * @var array<string, string>
     */
    protected static array $events = [
        Events::TEST_BEFORE => 'disableCollector'
    ];

    public function disableCollector(): void
    {
        gc_disable();
    }
}
