<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedEmmaFixture;
use Tests\Support\HttpClientComponentTrait;

final class SignupNewAccountWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientComponentTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testEnsureUserCanSignup(): void
    {
        $this->loadFixtures([AccountActivatedEmmaFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signup', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseContentSame(expectedContent: '');
        $this->assertEmailCount(count: 1);
    }

    public function testTryToSignupWithInvalidCredentials(): void
    {
        $this->sendPostRequest(url: '/api/auth/signup', params: [
            'email' => 'example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSignupWithNonexistentCredentials(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendPostRequest(url: '/api/auth/signup', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSignupWithInvalidTypes(): void
    {
        $this->sendPostRequest(url: '/api/auth/signup', params: [
            'email' => 'example.com',
            'password' => ['password4#account'],
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
