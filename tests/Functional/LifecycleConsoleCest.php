<?php

declare(strict_types=1);

namespace Tests\Functional;

use Tests\Support\FunctionalTester;

final class LifecycleConsoleCest
{
    public function runAppSymfonyRunCommand(FunctionalTester $i): void
    {
        $i->runSymfonyConsoleCommand(command: 'app:symfony:run');
    }
}
