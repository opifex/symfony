<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Contract\HttpbinResponderInterface;
use App\Domain\Exception\HttpbinResponderException;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Exclude]
final class KennethreitzHttpbinAdapter implements HttpbinResponderInterface
{
    public function __construct(private HttpClientInterface $httpbinClient)
    {
    }

    #[Override]
    public function getJson(): array
    {
        try {
            return $this->httpbinClient->request('GET', 'json')->toArray();
        } catch (ExceptionInterface $e) {
            throw new HttpbinResponderException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
