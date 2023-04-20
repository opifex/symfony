<?php

declare(strict_types=1);

namespace App\Application\Listener\Kernel;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[AsEventListener(event: ControllerArgumentsEvent::class)]
class ControllerListener
{
    public function __construct(private DenormalizerInterface $denormalizer)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ControllerArgumentsEvent $event): void
    {
        $attributes = (array)$event->getRequest()->attributes->get(key: '_route_params', default: []);
        $request = $this->filterParameters(array_merge($event->getRequest()->query->all(), $attributes));

        $arguments = $event->getArguments();

        foreach ($arguments as &$argument) {
            if ($argument instanceof MessageInterface) {
                $argument = $this->denormalizer->denormalize($request, $argument::class, context: [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ]);
            }
        }

        $event->setArguments($arguments);
    }

    /**
     * @param array&array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function filterParameters(array $data): array
    {
        return array_filter($data, fn(string $key) => !str_starts_with($key, '_'), mode: ARRAY_FILTER_USE_KEY);
    }
}
