<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\HttpbinResponseProviderInterface;
use App\Presentation\Command\SymfonyRunCommand;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SymfonyRunCommandTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->clock = $this->createMock(type: ClockInterface::class);
        $this->httpbinResponseProvider = $this->createMock(type: HttpbinResponseProviderInterface::class);
    }

    public function testExecuteWithSuccessResult(): void
    {
        $command = new SymfonyRunCommand($this->clock, $this->httpbinResponseProvider);

        $output = new BufferedOutput();
        $symfonyStyle = new SymfonyStyle(new ArrayInput([]), $output);

        $result = $command($symfonyStyle, delaySeconds: 0);

        $this->assertSame(expected: Command::SUCCESS, actual: $result);
        $this->assertStringContainsString(needle: 'Success', haystack: $output->fetch());
    }
}
