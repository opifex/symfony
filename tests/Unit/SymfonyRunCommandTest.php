<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Contract\HttpbinResponderInterface;
use App\Presentation\Command\SymfonyRunCommand;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyRunCommandTest extends Unit
{
    private Application $application;

    private HttpbinResponderInterface&MockObject $httpbinResponder;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->application = new Application();
        $this->httpbinResponder = $this->createMock(type: HttpbinResponderInterface::class);
        $this->application->add(
            new SymfonyRunCommand(
                clock: new MockClock(),
                httpbinResponder: $this->httpbinResponder,
            ),
        );
    }

    public function testExecuteWithSuccessResult(): void
    {
        $this->httpbinResponder
            ->expects($this->once())
            ->method(constraint: 'getJson')
            ->willReturn(['slideshow' => ['title' => 'Sample Slide Show']]);

        $commandTester = new CommandTester($this->application->get('app:symfony:run'));
        $commandTester->execute(['--delay' => 0]);

        $this->assertSame(expected: Command::SUCCESS, actual: $commandTester->getStatusCode());
        $this->assertStringContainsString(needle: '[OK] Success', haystack: $commandTester->getDisplay());
    }
}
