<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Presentation\Command\SymfonyRunCommand;
use Override;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyRunCommandTest extends KernelTestCase
{
    private readonly Command $command;

    #[Override]
    protected function setUp(): void
    {
        $application = new Application(self::bootKernel());
        $application->add(new SymfonyRunCommand(new MockClock()));
        $this->command = $application->find(name: 'app:symfony:run');
    }

    public function testEnsureConsoleCommandExecutesSuccessfully(): void
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(['--delay' => 0]);
        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString(needle: 'Symfony console command', haystack: $commandTester->getDisplay());
        $this->assertStringContainsString(needle: 'Success', haystack: $commandTester->getDisplay());
    }
}
