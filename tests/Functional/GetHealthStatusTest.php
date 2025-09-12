<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\HttpFoundation\Response;

final class GetHealthStatusTest extends AbstractWebTestCase
{
    public function testEnsureHealthStatusIsOk(): void
    {
        $this->sendGetRequest(url: '/api/health');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schema: 'GetHealthStatusSchema.json');
    }

    public function testTryToGetHealthWithInvalidMethod(): void
    {
        $this->sendPostRequest(url: '/api/health');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_METHOD_NOT_ALLOWED);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToGetHealthWithInvalidRoute(): void
    {
        $this->sendGetRequest(url: '/api/invalid');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
