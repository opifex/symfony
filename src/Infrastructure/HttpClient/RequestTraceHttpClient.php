<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpClient;

use App\Application\Contract\RequestTraceManagerInterface;
use App\Domain\Foundation\HttpSpecification;
use Override;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Service\ResetInterface;

#[AsDecorator(HttpClientInterface::class)]
final class RequestTraceHttpClient implements HttpClientInterface, ResetInterface
{
    use DecoratorTrait;

    public function __construct(
        private readonly RequestTraceManagerInterface $requestTraceManager,
        #[AutowireDecorated]
        ?HttpClientInterface $client = null,
    ) {
        $this->client = $client ?? HttpClient::create();
    }

    /**
     * @param array<string, mixed> $options
     * @throws TransportExceptionInterface
     */
    #[Override]
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $options['headers'] ??= [];

        if (is_array($options['headers'])) {
            $correlationId = $this->requestTraceManager->getCorrelationId();
            $options['headers'][HttpSpecification::HEADER_X_CORRELATION_ID] = $correlationId;
        }

        return $this->client->request($method, $url, $options);
    }
}
