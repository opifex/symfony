<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use App\Domain\Contract\HttpbinResponderInterface;
use Override;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:symfony:run', description: 'Symfony console command')]
final class SymfonyRunCommand extends Command
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly HttpbinResponderInterface $httpbinResponder,
    ) {
        parent::__construct();
    }

    #[Override]
    protected function configure(): void
    {
        $this->addOption(
            name: 'delay',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Delay in seconds between iterations.',
            default: 1,
        );
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $console = new SymfonyStyle($input, $output);
        $console->title($this->getDescription());

        $delay = is_string($input->getOption(name: 'delay')) ? (int) $input->getOption(name: 'delay') : 0;

        $iterableItems = [$this->httpbinResponder->getJson()];

        foreach ($console->progressIterate($iterableItems) as $item) {
            $this->clock->sleep($delay);
        }

        $console->success('Success');

        return self::SUCCESS;
    }
}
