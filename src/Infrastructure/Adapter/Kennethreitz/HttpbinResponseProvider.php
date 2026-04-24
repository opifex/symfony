<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Kennethreitz;

use App\Application\Contract\HttpbinResponseProviderInterface;
use App\Infrastructure\Adapter\Kennethreitz\Exception\HttpRequestFailedException;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpbinResponseProvider implements HttpbinResponseProviderInterface
{
    public function __construct(
        #[Autowire(env: 'HTTPBIN_URL')]
        private string $apiUrl,
        private HttpClientInterface $httpClient,
    ) {
    }

    #[Override]
    public function getJson(): array
    {
        try {
            /** @var array<array-key, mixed> */
            return $this->httpClient->withOptions([
                'base_uri' => $this->apiUrl,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ])->request(method: 'GET', url: 'json')->toArray();
        } catch (ExceptionInterface $exception) {
            throw HttpRequestFailedException::fromException($exception);
        }
    }
}
