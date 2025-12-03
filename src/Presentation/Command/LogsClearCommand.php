<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(name: 'logs:clear', description: 'Clears log files for the current environment')]
final class LogsClearCommand
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly KernelInterface $kernel,
    ) {
    }

    public function __invoke(SymfonyStyle $symfonyStyle): int
    {
        $envName = $this->kernel->getEnvironment();
        $logFile = $this->kernel->getLogDir() . '/' . $envName . '.log';

        $this->filesystem->remove($logFile);

        if ($this->filesystem->exists($logFile)) {
            throw new RuntimeException(sprintf('Unable to remove the "%s" file.', $logFile));
        }

        $symfonyStyle->success(sprintf('Logs for the "%s" environment were successfully cleared.', $envName));

        return Command::SUCCESS;
    }
}
