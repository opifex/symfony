<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;

final class CreateNewAccountTest extends AbstractWebTestCase
{
    public function testEnsureAdminCanCreateAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account', params: [
            'email' => 'created@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_CREATED);
        $this->assertResponseSchema(schema: 'CreateNewAccountSchema.json');
    }

    public function testTryToCreateAccountWithExistingEmail(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToCreateAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account', params: [
            'email' => 'created@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
