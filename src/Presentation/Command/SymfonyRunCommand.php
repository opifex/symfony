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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:symfony:run', description: 'Symfony console command')]
final class SymfonyRunCommand extends Command
{
    public function __construct(
        private ClockInterface $clock,
        private HttpbinResponderInterface $httpbinResponder,
        private ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    #[Override]
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
            description: 'Delay in seconds between iterations.',
            default: 1,
        );
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $console = new SymfonyStyle($input, $output);
        $console->title($this->getDescription());

        /** @var int $count */
        $count = $input->getOption(name: 'count');
        /** @var int $delay */
        $delay = $input->getOption(name: 'delay');

        $violations = $this->validator->validate(
            value: ['count' => $count, 'delay' => $delay],
            constraints: new Assert\Collection([
                'count' => [new Assert\DivisibleBy(value: 1), new Assert\Positive()],
                'delay' => [new Assert\DivisibleBy(value: 1), new Assert\PositiveOrZero()],
            ]),
        );

        if ($violations->count()) {
            $console->error($violations->get(0)->getPropertyPath() . ' ' . $violations->get(0)->getMessage());

            return self::FAILURE;
        }

        $iterableItems = array_pad([$this->httpbinResponder->getJson()], length: $count, value: null);

        foreach ($console->progressIterate($iterableItems) as $item) {
            if ($item !== null) {
                $this->clock->sleep($delay);
            }
        }

        $console->success('Success');

        return self::SUCCESS;
    }
}
