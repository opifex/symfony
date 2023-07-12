<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:symfony:run', description: 'Symfony console command')]
final class SymfonyRunCommand extends Command
{
    public function __construct(private ClockInterface $clock)
    {
        parent::__construct();
    }

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

        $count = $input->getOption(name: 'count');
        $delay = $input->getOption(name: 'delay');

        if (!is_numeric($count) || $count <= 0) {
            throw new InvalidOptionException(message: 'Count value should be positive.');
        }

        if (!is_numeric($delay) || $delay < 0) {
            throw new InvalidOptionException(message: 'Delay value should be either positive or zero.');
        }

        [$count, $delay] = [intval($count), intval($delay)];

        foreach ($console->progressIterate(array_pad([], length: $count, value: null)) as $item) {
            if ($item === null) {
                $this->clock->sleep($delay);
            }
        }

        $console->success('Success');

        return self::SUCCESS;
    }
}
