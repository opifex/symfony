<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Presentation\Command\SymfonyRunCommand;
use Codeception\Test\Unit;
use Override;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyRunCommandTest extends Unit
{
    private Application $application;

    #[Override]
    protected function setUp(): void
    {
        $this->application = new Application();
        $this->application->add(new SymfonyRunCommand(new MockClock()));
    }

    public function testExecuteWithSuccessResult(): void
    {
        $commandTester = new CommandTester($this->application->get('app:symfony:run'));
        $commandTester->execute(['--delay' => 0]);

        $this->assertSame(expected: Command::SUCCESS, actual: $commandTester->getStatusCode());
        $this->assertStringContainsString(needle: '[OK] Success', haystack: $commandTester->getDisplay());
    }
}
