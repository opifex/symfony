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
    }

    public function testInvokeWithConsoleCommandEvent(): void
    {
        $consoleEventListener = new ConsoleEventListener($this->logger);
        $consoleCommandEvent = new ConsoleCommandEvent($this->command, $this->input, $this->output);
        ($consoleEventListener)($consoleCommandEvent);

        $this->expectNotToPerformAssertions();
    }
}
