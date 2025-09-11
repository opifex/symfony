<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;

final class GetSigninAccountTest extends AbstractWebTestCase
{
    public function testEnsureUserCanGetSigninAccountWithValidBearer(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendGetRequest(url: '/api/auth/me');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schemaFile: 'GetSigninAccountSchema.json');
    }

    public function testTryToGetSigninAccountWithoutAuthorizationHeader(): void
    {
        $this->sendGetRequest(url: '/api/auth/me');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToGetSigninAccountWithInvalidAuthorizationHeader(): void
    {
        $this->getClient()->setServerParameter('HTTP_Authorization', 'invalid');
        $this->getClient()->request(method: Request::METHOD_GET, uri: '/api/auth/me');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToGetSigninAccountWithInvalidBearerToken(): void
    {
        $this->getClient()->setServerParameter('HTTP_Authorization', 'Bearer invalid');
        $this->getClient()->request(method: Request::METHOD_GET, uri: '/api/auth/me');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
