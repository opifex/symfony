<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpClient;

use App\Domain\Contract\RequestIdStorageInterface;
use App\Domain\Entity\HttpSpecification;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Service\ResetInterface;

#[AsDecorator(HttpClientInterface::class)]
final class RequestIdHttpClient implements HttpClientInterface, ResetInterface
{
    use DecoratorTrait;

    public function __construct(
        private readonly RequestIdStorageInterface $requestIdStorage,

        #[AutowireDecorated]
        ?HttpClientInterface $client = null,
    ) {
        $this->client = $client ?? HttpClient::create();
    }

    /**
     * @param array<string, mixed> $options
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $requestId = $this->requestIdStorage->getRequestId();
        $options['headers'] ??= [];

        if ($requestId !== null && is_array($options['headers'])) {
            $options['headers'][HttpSpecification::HEADER_X_REQUEST_ID] = $requestId;
        }

        return $this->client->request($method, $url, $options);
    }
}
