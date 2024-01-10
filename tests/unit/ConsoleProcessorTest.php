<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Logging\ConsoleProcessor;
use Codeception\Test\Unit;
use DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleProcessorTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->command = $this->createMock(originalClassName: Command::class);
        $this->date = $this->createMock(originalClassName: DateTimeImmutable::class);
        $this->input = $this->createMock(originalClassName: InputInterface::class);
        $this->output = $this->createMock(originalClassName: OutputInterface::class);
    }

    public function testLogConsoleCommandRun(): void
    {
        $consoleProcessor = new ConsoleProcessor();
        $commandName = 'app:command:action';

        $this->command
            ->expects($this->once())
            ->method(constraint: 'getName')
            ->willReturn($commandName);

        $this->input
            ->expects($this->once())
            ->method(constraint: 'getArguments')
            ->willReturn(value: ['command' => $commandName]);

        $this->input
            ->expects($this->once())
            ->method(constraint: 'getArguments')
            ->willReturn(value: ['option' => 'value']);

        $logRecord = new LogRecord($this->date, channel: '', level: Level::Info, message: '');
        $consoleCommandEvent = new ConsoleCommandEvent($this->command, $this->input, $this->output);
        $consoleProcessor->onConsoleCommand($consoleCommandEvent);
        $result = ($consoleProcessor)($logRecord);

        $this->assertArrayHasKey(key: 'console', array: $result->extra);
        $this->assertArrayHasKey(key: 'command', array: $result->extra['console']);
        $this->assertSame($commandName, $result->extra['console']['command']);
    }
}
