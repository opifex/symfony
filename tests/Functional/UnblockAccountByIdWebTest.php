<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\Fixture\AccountBlockedHenryFixture;
use Tests\Support\HttpClientRequestsTrait;

final class UnblockAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::loadHttpClient();
    }

    public function testEnsureAdminCanUnblockBlockedAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class, AccountBlockedHenryFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountHenry = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'henry@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountHenry);
        self::sendPostRequest(url: '/api/account/' . $accountHenry->id . '/unblock');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
    }

    public function testTryToUnblockNonexistentAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendPostRequest(url: '/api/account/00000000-0000-6000-8000-000000000000/unblock');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        self::assertErrorResponseSchema();
    }

    public function testTryToUnblockBlockedAccountWithoutPermission(): void
    {
        self::loadFixtures([AccountActivatedJamesFixture::class, AccountBlockedHenryFixture::class]);
        self::sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $accountHenry = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'henry@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountHenry);
        self::sendPostRequest(url: '/api/account/' . $accountHenry->id . '/unblock');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        self::assertErrorResponseSchema();
    }
}
