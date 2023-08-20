<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Messenger\ResponseStamp;
use App\Application\Messenger\TemplateStamp;
use App\Application\Serializer\HtmlTemplateEncoder;
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
final class ViewEventListener
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
            $templateStamp = $controllerResult->last(stampFqcn: TemplateStamp::class);

            $format = $event->getRequest()->getPreferredFormat(default: JsonEncoder::FORMAT) ?? '';
            $status = $responseStamp instanceof ResponseStamp ? $responseStamp->getCode() : null;
            $context = $serializerStamp instanceof SerializerStamp ? $serializerStamp->getContext() : [];
            $headers = $responseStamp instanceof ResponseStamp ? $responseStamp->getHeaders() : [];
            $result = $handledStamp instanceof HandledStamp ? $handledStamp->getResult() : '';

            $context[JsonEncode::OPTIONS] = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

            if ($templateStamp instanceof TemplateStamp) {
                $context[HtmlTemplateEncoder::TEMPLATE] = $templateStamp->getTemplate();
            }

            if (is_countable($result) && !is_array($result)) {
                $headers['X-Total-Count'] = count($result);
            }

            $content = $this->serializer->serialize($result, $format, $context);
            $status = $status ?? (empty($result) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK);

            $event->setResponse(new Response($content, $status, $headers));
        }
    }
}
