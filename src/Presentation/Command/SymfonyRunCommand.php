<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:symfony:run', description: 'Symfony console command')]
class SymfonyRunCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $console = new SymfonyStyle($input, $output);
        $console->title($this->getDescription());

        foreach ($console->progressIterate(array_pad([], length: 3, value: null)) as $item) {
            if ($item === null) {
                sleep(seconds: 1);
            }
        }

        $console->success('Success');

        return self::SUCCESS;
    }
}
