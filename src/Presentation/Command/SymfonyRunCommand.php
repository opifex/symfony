<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:symfony:run', description: 'Symfony console command')]
class SymfonyRunCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption(
            name: 'count',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Amount of iterations to process.',
            default: 1,
        );
        $this->addOption(
            name: 'delay',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Delay between iterations in seconds.',
            default: 1,
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $console = new SymfonyStyle($input, $output);
        $console->title($this->getDescription());

        $count = intval($input->getOption(name: 'count'));
        $delay = intval($input->getOption(name: 'delay'));

        foreach ($console->progressIterate(array_pad([], length: $count, value: null)) as $item) {
            if ($item === null) {
                sleep($delay);
            }
        }

        $console->success('Success');

        return self::SUCCESS;
    }
}
