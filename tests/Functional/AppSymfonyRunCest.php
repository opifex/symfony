<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\Console\Command\Command;
use Tests\Support\FunctionalTester;

final class AppSymfonyRunCest
{
    public function runCommandUsingValidParameters(FunctionalTester $i): void
    {
        $i->runSymfonyConsoleCommand(
            command: 'app:symfony:run',
            parameters: ['--count' => 1, '--delay' => 0],
            expectedExitCode: Command::SUCCESS,
        );
    }

    public function runCommandUsingInvalidParameters(FunctionalTester $i): void
    {
        $i->runSymfonyConsoleCommand(
            command: 'app:symfony:run',
            parameters: ['--count' => -1],
            expectedExitCode: Command::FAILURE,
        );
    }
}
