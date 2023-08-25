<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\ConsoleEventListener;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleEventListenerTest extends Unit
{
    private Command&MockObject $command;
    private ConsoleEventListener $consoleEventListener;
    private InputInterface&MockObject $input;
    private OutputInterface&MockObject $output;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $logger = $this->createMock(originalClassName: LoggerInterface::class);
        $this->consoleEventListener = new ConsoleEventListener($logger);
        $this->command = $this->createMock(originalClassName: Command::class);
        $this->input = $this->createMock(originalClassName: InputInterface::class);
        $this->output = $this->createMock(originalClassName: OutputInterface::class);
    }

    public function testInvokeWithConsoleCommandEvent(): void
    {
        $consoleCommandEvent = new ConsoleCommandEvent($this->command, $this->input, $this->output);

        ($this->consoleEventListener)($consoleCommandEvent);
    }
}
