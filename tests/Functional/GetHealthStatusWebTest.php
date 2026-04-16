<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\HttpClientRequestsTrait;

final class GetHealthStatusWebTest extends WebTestCase
{
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::loadHttpClient();
    }

    public function testEnsureHealthStatusIsOk(): void
    {
        self::sendGetRequest(url: '/api/health');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertResponseSchema();
    }

    public function testTryToGetHealthWithInvalidMethod(): void
    {
        self::sendPostRequest(url: '/api/health');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testTryToGetHealthWithInvalidRoute(): void
    {
        self::sendGetRequest(url: '/api/invalid');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
    }
}
