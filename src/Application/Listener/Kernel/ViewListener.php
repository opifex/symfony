<?php

declare(strict_types=1);

namespace App\Application\Listener\Kernel;

use App\Domain\Messenger\ResponseStamp;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: ViewEvent::class)]
class ViewListener
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function __invoke(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();

        if ($controllerResult instanceof Envelope) {
            $handledStamp = $controllerResult->last(stampFqcn: HandledStamp::class);
            $responseStamp = $controllerResult->last(stampFqcn: ResponseStamp::class);
            $serializerStamp = $controllerResult->last(stampFqcn: SerializerStamp::class);

            $format = $event->getRequest()->getPreferredFormat(default: JsonEncoder::FORMAT) ?? '';
            $code = $responseStamp instanceof ResponseStamp ? $responseStamp->getCode() : null;
            $context = $serializerStamp instanceof SerializerStamp ? $serializerStamp->getContext() : [];
            $headers = $responseStamp instanceof ResponseStamp ? $responseStamp->getHeaders() : [];
            $result = $handledStamp instanceof HandledStamp ? $handledStamp->getResult() : '';
            $context[JsonEncode::OPTIONS] = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

            if (is_countable($result) && !is_array($result)) {
                $headers['X-Total-Count'] = count($result);
            }

            $response = new Response(
                content: $this->serializer->serialize($result, $format, $context),
                status: $code ?? (empty($result) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK),
                headers: $headers,
            );

            $event->setResponse($response);
        }
    }
}
