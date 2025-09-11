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
        $this->loadFixture([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account', parameters: [
            'email' => 'created@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_CREATED);
        $this->assertResponseSchema(schemaFile: 'CreateNewAccountSchema.json');
    }

    public function testTryToCreateAccountWithExistingEmail(): void
    {
        $this->loadFixture([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account', parameters: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToCreateAccountWithoutPermission(): void
    {
        $this->loadFixture([AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account', parameters: [
            'email' => 'created@example.com',
            'password' => 'password4#account',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
