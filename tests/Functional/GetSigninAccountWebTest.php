<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\HttpClientRequestsTrait;

final class GetSigninAccountWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::loadHttpClient();
    }

    public function testGetSigninAccountReturnsCurrentUser(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendGetRequest(url: '/api/auth/me');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertResponseSchema();
    }

    public function testGetSigninAccountReturnsUnauthorizedWithoutHeader(): void
    {
        self::sendGetRequest(url: '/api/auth/me');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        self::assertErrorResponseSchema();
    }

    public function testGetSigninAccountReturnsUnauthorizedForInvalidHeader(): void
    {
        self::sendGetRequest(url: '/api/auth/me', server: ['HTTP_Authorization' => 'invalid']);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        self::assertErrorResponseSchema();
    }

    public function testGetSigninAccountReturnsUnauthorizedForInvalidToken(): void
    {
        self::sendGetRequest(url: '/api/auth/me', server: ['HTTP_Authorization' => 'Bearer invalid']);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        self::assertErrorResponseSchema();
    }
}
