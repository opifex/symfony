<?php

declare(strict_types=1);

namespace App\Application\Listener;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: ViewEvent::class)]
final class KernelViewEventListener
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    /**
     * @throws ReflectionException
     */
    public function __invoke(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();

        if ($controllerResult instanceof Envelope) {
            $handledStamp = $controllerResult->last(stampFqcn: HandledStamp::class);

            $format = $event->getRequest()->getPreferredFormat(default: JsonEncoder::FORMAT) ?? '';
            $result = $handledStamp instanceof HandledStamp ? $handledStamp->getResult() : '';

            $objectClass = is_object($result) ? new ReflectionClass($result) : null;
            $httpStatus = ($objectClass?->getAttributes(name: WithHttpStatus::class)[0] ?? null)?->newInstance();

            $statusCode = $httpStatus instanceof WithHttpStatus ? $httpStatus->statusCode : null;
            $headers = $httpStatus instanceof WithHttpStatus ? $httpStatus->headers : [];

            $context = [JsonEncode::OPTIONS => JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT];

            if (is_countable($result) && !is_array($result)) {
                $headers['X-Total-Count'] = count($result);
            }

            $content = $this->serializer->serialize($result, $format, $context);
            $statusCode = $statusCode ?? (empty($result) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK);

            $event->setResponse(new Response($content, $statusCode, $headers));
        }
    }
}
