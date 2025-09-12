<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountRegisteredOliviaFixture;

final class SigninIntoAccountTest extends AbstractWebTestCase
{
    public function testEnsureAdminCanSignin(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signin', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schema: 'SigninIntoAccountSchema.json');
    }

    public function testTryToSigninWithNonactivatedUser(): void
    {
        $this->loadFixtures([AccountRegisteredOliviaFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signin', params: [
            'email' => 'olivia@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSigninWithInvalidCredentials(): void
    {
        $this->sendPostRequest(url: '/api/auth/signin', params: [
            'email' => 'invalid@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSigninWithExtraAttributes(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signin', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'extra' => 'value',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
