<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Presentation\Command\LogsClearCommand;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final class LogsClearCommandTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(type: Filesystem::class);
        $this->kernel = $this->createMock(type: KernelInterface::class);
    }

    public function testExecuteWithSuccessResult(): void
    {
        $command = new LogsClearCommand($this->filesystem, $this->kernel);

        $output = new BufferedOutput();
        $symfonyStyle = new SymfonyStyle(new ArrayInput([]), $output);

        $result = $command($symfonyStyle);

        $this->assertSame(expected: Command::SUCCESS, actual: $result);
    }

    public function testExecuteWithUnableToRemoveFileResult(): void
    {
        $command = new LogsClearCommand($this->filesystem, $this->kernel);

        $this->filesystem
            ->expects($this->once())
            ->method(constraint: 'exists')
            ->willReturn(value: true);

        $output = new BufferedOutput();
        $symfonyStyle = new SymfonyStyle(new ArrayInput([]), $output);

        $this->expectException(exception: RuntimeException::class);

        $command($symfonyStyle);
    }
}
