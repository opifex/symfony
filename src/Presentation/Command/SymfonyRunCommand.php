<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use App\Application\Contract\HttpbinResponseProviderInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
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
        SymfonyStyle $symfonyStyle,
        #[Option(description: 'Delay in seconds between iterations.', name: 'delay')]
        int $delaySeconds = 1,
    ): int {
        $symfonyStyle->title($this->getDescription());

        $slideshow = (array) ($this->httpbinResponseProvider->getJson()['slideshow'] ?? []);
        $slides = (array) ($slideshow['slides'] ?? []);

        foreach ($symfonyStyle->progressIterate($slides) as $slide) {
            $this->clock->sleep($delaySeconds);
        }

        $symfonyStyle->success('Success');

        return self::SUCCESS;
    }
}
