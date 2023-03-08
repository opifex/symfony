<?php

declare(strict_types=1);

namespace App\Tests;

use App\Presentation\Command\SymfonyRunCommand;
use Codeception\Test\Unit;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyRunCommandTest extends Unit
{
    private Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();
        $this->application->add(new SymfonyRunCommand());
    }

    public function testExecuteWithSuccessResult(): void
    {
        $commandTester = new CommandTester($this->application->get('app:symfony:run'));
        $commandTester->execute([]);

        $this->assertSame(expected: Command::SUCCESS, actual: $commandTester->getStatusCode());
        $this->assertStringContainsString(needle: '[OK] Success', haystack: $commandTester->getDisplay());
    }
}
