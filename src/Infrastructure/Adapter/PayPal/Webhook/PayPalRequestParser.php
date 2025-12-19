<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\PayPal\Webhook;

use App\Domain\Foundation\HttpSpecification;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPayloadConverter;
use Override;
use SensitiveParameter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\RemoteEvent\Exception\ParseException;
use Symfony\Component\RemoteEvent\PayloadConverterInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class PayPalRequestParser extends AbstractRequestParser
{
    private const string WEBHOOK_TOKEN_PARAM = 'token';

    public function __construct(
        #[Autowire(service: PayPalPayloadConverter::class)]
        private readonly PayloadConverterInterface $payloadConverter,
    ) {
    }

    #[Override]
    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new MethodRequestMatcher([Request::METHOD_POST]),
            new IsJsonRequestMatcher(),
        ]);
    }

    #[Override]
    protected function doParse(Request $request, #[SensitiveParameter] string $secret): RemoteEvent
    {
        $authorizationToken = $request->query->getString(key: self::WEBHOOK_TOKEN_PARAM);

        if ($authorizationToken === '' || $authorizationToken !== $secret) {
            throw new RejectWebhookException(
                statusCode: HttpSpecification::HTTP_UNAUTHORIZED,
                message: 'Invalid or missing webhook token.',
            );
        }

        try {
            return $this->payloadConverter->convert($request->toArray());
        } catch (ParseException $e) {
            throw new RejectWebhookException(message: 'Webhook payload could not be parsed.', previous: $e);
        }
    }
}
