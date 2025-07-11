<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Kennethreitz;

use App\Domain\Contract\Integration\HttpbinResponseProviderInterface;
use App\Domain\Exception\Integration\HttpbinResponseProviderException;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class KennethreitzHttpbinAdapter implements HttpbinResponseProviderInterface
{
    public function __construct(
        #[Autowire('%env(HTTPBIN_URL)%')]
        private readonly string $apiUrl,

        private readonly HttpClientInterface $httpClient,
    ) {
    }

    #[Override]
    public function getJson(): array
    {
        try {
            /** @var array<string, mixed> */
            return $this->httpClient->withOptions([
                'base_uri' => $this->apiUrl,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ])->request(method: 'GET', url: 'json')->toArray();
        } catch (ExceptionInterface $e) {
            throw HttpbinResponseProviderException::fromException($e);
        }
    }
}
