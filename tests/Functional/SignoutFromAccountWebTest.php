<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\HttpClientRequestsTrait;

final class SignoutFromAccountWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::loadHttpClient();
    }

    public function testEnsureAuthenticatedUserCanSignout(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendPostRequest(url: '/api/auth/signout');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
    }

    public function testEnsureSignoutInvalidatesToken(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendPostRequest(url: '/api/auth/signout');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        self::sendPostRequest(url: '/api/auth/signout');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
    }

    public function testSignoutRequiresAuthentication(): void
    {
        self::sendPostRequest(url: '/api/auth/signout');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
    }
}
