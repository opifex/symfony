<?php

declare(strict_types=1);

namespace App\Application\Listener\Kernel;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsEventListener(event: ControllerArgumentsEvent::class)]
final class ControllerListener
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ControllerArgumentsEvent $event): void
    {
        $request = (array)$this->normalizer->normalize($event->getRequest());
        $attributes = (array)$event->getRequest()->attributes->get(key: '_route_params');
        $parameters = $this->filterParameters(array_merge($request, $attributes));

        $arguments = $event->getArguments();

        foreach ($arguments as &$argument) {
            if ($argument instanceof MessageInterface) {
                $argument = $this->denormalizer->denormalize($parameters, $argument::class, context: [
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
