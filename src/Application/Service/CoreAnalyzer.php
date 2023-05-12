<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Adapter\HttpbinAdapterInterface;
use App\Domain\Exception\Adapter\HttpbinAdapterException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

final class CoreAnalyzer
{
    public function __construct(
        private CacheInterface $cacheStorage,
        private HttpbinAdapterInterface $httpbinAdapter,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cache(string $key, string $value): string
    {
        return $this->cacheStorage->get($key, fn() => $value);
    }

    /**
     * @return array<string, mixed>
     * @throws HttpbinAdapterException
     */
    public function httpbin(): array
    {
        return $this->httpbinAdapter->getJson();
    }
}
