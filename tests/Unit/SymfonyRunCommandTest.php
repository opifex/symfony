<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Presentation\Command\SymfonyRunCommand;
use Codeception\Test\Unit;
use Override;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyRunCommandTest extends Unit
{
    #[Override]
    protected function setUp(): void
    {
        $this->application = new Application();
        $this->application->add(new SymfonyRunCommand(new MockClock()));
    }

    public function testExecuteWithSuccessResult(): void
    {
        $commandTester = new CommandTester($this->application->get('app:symfony:run'));
        $commandTester->execute(['--count' => 1, '--delay' => 0]);

        $this->assertSame(expected: Command::SUCCESS, actual: $commandTester->getStatusCode());
        $this->assertStringContainsString(needle: '[OK] Success', haystack: $commandTester->getDisplay());
    }

    public function testExecuteThrowsExceptionOnCountOptionInvalid(): void
    {
        $commandTester = new CommandTester($this->application->get('app:symfony:run'));

        $this->expectException(InvalidOptionException::class);

        $commandTester->execute(['--count' => -1, '--delay' => 0]);
    }

    public function testExecuteThrowsExceptionOnDelayOptionInvalid(): void
    {
        $commandTester = new CommandTester($this->application->get('app:symfony:run'));

        $this->expectException(InvalidOptionException::class);

        $commandTester->execute(['--count' => 1, '--delay' => -1]);
    }
}
