<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\HttpbinResponseProviderInterface;
use App\Presentation\Command\SymfonyRunCommand;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyRunCommandTest extends TestCase
{
    private Application $application;

    private HttpbinResponseProviderInterface&MockObject $httpbinResponseProvider;

    #[Override]
    protected function setUp(): void
    {
        $this->application = new Application();
        $this->httpbinResponseProvider = $this->createMock(type: HttpbinResponseProviderInterface::class);
        $this->application->add(new SymfonyRunCommand(new MockClock(), $this->httpbinResponseProvider));
    }

    public function testExecuteWithSuccessResult(): void
    {
        $commandTester = new CommandTester($this->application->get('app:symfony:run'));
        $commandTester->execute(['--delay' => 0]);

        $this->assertSame(expected: Command::SUCCESS, actual: $commandTester->getStatusCode());
        $this->assertStringContainsString(needle: '[OK] Success', haystack: $commandTester->getDisplay());
    }
}
