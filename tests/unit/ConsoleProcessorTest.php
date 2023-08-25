<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Logging\ConsoleProcessor;
use Codeception\Test\Unit;
use DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleProcessorTest extends Unit
{
    private Command&MockObject $command;
    private ConsoleProcessor $consoleProcessor;
    private DateTimeImmutable&MockObject $date;
    private InputInterface&MockObject $input;
    private OutputInterface&MockObject $output;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->command = $this->createMock(originalClassName: Command::class);
        $this->date = $this->createMock(originalClassName: DateTimeImmutable::class);
        $this->input = $this->createMock(originalClassName: InputInterface::class);
        $this->output = $this->createMock(originalClassName: OutputInterface::class);
        $this->consoleProcessor = new ConsoleProcessor();
    }

    public function testInvokeWithCache(): void
    {
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
        $this->consoleProcessor->onConsoleCommand($consoleCommandEvent);

        $result = ($this->consoleProcessor)($logRecord);

        $this->assertArrayHasKey(key: 'console', array: $result->extra);
        $this->assertArrayHasKey(key: 'command', array: $result->extra['console']);
        $this->assertEquals($commandName, $result->extra['console']['command']);
    }
}
