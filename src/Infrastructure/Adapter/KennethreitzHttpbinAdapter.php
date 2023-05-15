<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Contract\Adapter\HttpbinAdapterInterface;
use App\Domain\Exception\Adapter\HttpbinAdapterException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class KennethreitzHttpbinAdapter implements HttpbinAdapterInterface
{
    public function __construct(private HttpClientInterface $httpbinClient)
    {
    }

    public function getJson(): array
    {
        try {
            return $this->httpbinClient->request('GET', 'json')->toArray();
        } catch (ExceptionInterface $e) {
            throw new HttpbinAdapterException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
