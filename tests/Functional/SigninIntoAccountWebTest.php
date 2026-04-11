<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountRegisteredOliviaFixture;
use Tests\Support\HttpClientRequestsTrait;

final class SigninIntoAccountWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::activateHttpClient();
    }

    public function testEnsureAdminCanSignin(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendPostRequest(url: '/api/auth/signin', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertResponseSchema(schema: 'SigninIntoAccountSchema.json');
    }

    public function testTryToSigninWithNonactivatedUser(): void
    {
        self::loadFixtures([AccountRegisteredOliviaFixture::class]);
        self::sendPostRequest(url: '/api/auth/signin', params: [
            'email' => 'olivia@example.com',
            'password' => 'password4#account',
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSigninWithInvalidCredentials(): void
    {
        self::sendPostRequest(url: '/api/auth/signin', params: [
            'email' => 'invalid@example.com',
            'password' => 'password4#account',
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSigninWithExtraAttributes(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendPostRequest(url: '/api/auth/signin', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'extra' => 'value',
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
