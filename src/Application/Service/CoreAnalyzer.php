<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\CoreAnalyzerInterface;
use App\Domain\Contract\HttpbinAdapterInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

final class CoreAnalyzer implements CoreAnalyzerInterface
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

    public function httpbin(): array
    {
        return $this->httpbinAdapter->getJson();
    }
}
