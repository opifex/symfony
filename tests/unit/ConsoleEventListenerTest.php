<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\ConsoleEventListener;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleEventListenerTest extends Unit
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->command = $this->createMock(originalClassName: Command::class);
        $this->input = $this->createMock(originalClassName: InputInterface::class);
        $this->logger = $this->createMock(originalClassName: LoggerInterface::class);
        $this->output = $this->createMock(originalClassName: OutputInterface::class);

        $this->consoleEventListener = new ConsoleEventListener($this->logger);
    }

    public function testInvokeWithConsoleCommandEvent(): void
    {
        $consoleCommandEvent = new ConsoleCommandEvent($this->command, $this->input, $this->output);

        ($this->consoleEventListener)($consoleCommandEvent);

        $this->expectNotToPerformAssertions();
    }
}
