<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Presentation\Command\SymfonyRunCommand;
use Override;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyRunCommandTest extends KernelTestCase
{
    private Application $application;

    #[Override]
    protected function setUp(): void
    {
        $this->application = new Application(self::bootKernel());
        $this->application->add(new SymfonyRunCommand(new MockClock()));
    }

    public function testEnsureConsoleCommandExecutesSuccessfully(): void
    {
        $command = $this->application->find(name: 'app:symfony:run');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['--delay' => 0]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString(needle: 'Symfony console command', haystack: $commandTester->getDisplay());
        $this->assertStringContainsString(needle: 'Success', haystack: $commandTester->getDisplay());
    }
}
