<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\Fixture\AccountBlockedHenryFixture;

final class UnblockAccountByIdTest extends AbstractWebTestCase
{
    public function testEnsureAdminCanUnblockBlockedAccount(): void
    {
        $this->loadFixture([AccountActivatedAdminFixture::class, AccountBlockedHenryFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');

        /** @var AccountEntity $accountHenry */
        $accountHenry = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'henry@example.com'],
        );

        $this->sendPostRequest(url: '/api/account/' . $accountHenry->id . '/unblock');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseBodyIsEmpty();
    }

    public function testTryToUnblockNonexistentAccount(): void
    {
        $this->loadFixture([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account/00000000-0000-6000-8000-000000000000/unblock');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToUnblockBlockedAccountWithoutPermission(): void
    {
        $this->loadFixture([AccountActivatedJamesFixture::class, AccountBlockedHenryFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');

        /** @var AccountEntity $accountHenry */
        $accountHenry = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'henry@example.com'],
        );

        $this->sendPostRequest(url: '/api/account/' . $accountHenry->id . '/unblock');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
