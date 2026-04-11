<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedEmmaFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\Fixture\AccountBlockedHenryFixture;
use Tests\Support\HttpClientRequestsTrait;

final class BlockAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::activateHttpClient();
    }

    public function testEnsureAdminCanBlockActivatedAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountJames = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'james@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountJames);
        self::sendPostRequest(url: '/api/account/' . $accountJames->id . '/block');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        self::assertResponseContentSame(expectedContent: '');
    }

    public function testTryToBlockNonexistentAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendPostRequest(url: '/api/account/00000000-0000-6000-8000-000000000000/block');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToBlockAlreadyBlockedAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class, AccountBlockedHenryFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountHenry = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'henry@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountHenry);
        self::sendPostRequest(url: '/api/account/' . $accountHenry->id . '/block');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToBlockAccountWithoutPermission(): void
    {
        self::loadFixtures([AccountActivatedEmmaFixture::class, AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $accountEmma = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'emma@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountEmma);
        self::sendPostRequest(url: '/api/account/' . $accountEmma->id . '/block');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
