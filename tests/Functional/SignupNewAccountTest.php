<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;

final class SignupNewAccountTest extends AbstractWebTestCase
{
    public function testEnsureUserCanSignup(): void
    {
        $this->sendPostRequest(url: '/api/auth/signup', parameters: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseBodyIsEmpty();
    }

    public function testTryToSignupWithInvalidCredentials(): void
    {
        $this->sendPostRequest(url: '/api/auth/signup', parameters: [
            'email' => 'example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSignupWithNonexistentCredentials(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signup', parameters: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSignupWithInvalidTypes(): void
    {
        $this->sendPostRequest(url: '/api/auth/signup', parameters: [
            'email' => 'example.com',
            'password' => ['password4#account'],
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
