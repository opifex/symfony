<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use App\Domain\Contract\Integration\HttpbinResponseProviderInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:symfony:run', description: 'Symfony console command')]
final class SymfonyRunCommand extends Command
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly HttpbinResponseProviderInterface $httpbinResponseProvider,
    ) {
        parent::__construct();
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option(description: 'Delay in seconds between iterations.', name: 'delay')]
        int $delay = 1,
    ): int {
        $console = new SymfonyStyle($input, $output);
        $console->title($this->getDescription());

        $slides = $this->httpbinResponseProvider->getJson()['slideshow']['slides'] ?? [];

        foreach ($console->progressIterate($slides) as $slide) {
            $this->clock->sleep($delay);
        }

        $console->success('Success');

        return self::SUCCESS;
    }
}
