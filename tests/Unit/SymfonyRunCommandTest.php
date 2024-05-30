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
use Symfony\Component\Validator\Validation;

final class SymfonyRunCommandTest extends Unit
{
    #[Override]
    protected function setUp(): void
    {
        $this->clock = new MockClock();
        $this->validator = Validation::createValidator();
        $this->application = new Application();
        $this->application->add(new SymfonyRunCommand($this->clock, $this->validator));
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
        $commandTester->execute(['--count' => -1, '--delay' => 0]);

        $this->assertSame(expected: Command::FAILURE, actual: $commandTester->getStatusCode());
        $this->assertStringContainsString(
            needle: '[ERROR] [count] This value should be positive.',
            haystack: $commandTester->getDisplay(),
        );
    }

    public function testExecuteThrowsExceptionOnDelayOptionInvalid(): void
    {
        $commandTester = new CommandTester($this->application->get('app:symfony:run'));
        $commandTester->execute(['--count' => 1, '--delay' => -1]);

        $this->assertSame(expected: Command::FAILURE, actual: $commandTester->getStatusCode());
        $this->assertStringContainsString(
            needle: '[ERROR] [delay] This value should be either positive or zero.',
            haystack: $commandTester->getDisplay(),
        );
    }
}
