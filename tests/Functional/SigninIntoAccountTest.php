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
        $this->loadFixture([AccountActivatedAdminFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signin', parameters: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schemaFile: 'SigninIntoAccountSchema.json');
    }

    public function testTryToSigninWithNonactivatedUser(): void
    {
        $this->loadFixture([AccountRegisteredOliviaFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signin', parameters: [
            'email' => 'olivia@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSigninWithInvalidCredentials(): void
    {
        $this->sendPostRequest(url: '/api/auth/signin', parameters: [
            'email' => 'invalid@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSigninWithExtraAttributes(): void
    {
        $this->loadFixture([AccountActivatedAdminFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signin', parameters: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'extra' => 'value',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
