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
use Tests\Support\HttpClientRequestsTrait;

final class GetAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::loadHttpClient();
    }

    public function testEnsureAdminCanGetExistingAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountAdmin = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        self::sendGetRequest(url: '/api/account/' . $accountAdmin->id);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertResponseSchema();
    }

    public function testTryToGetNonexistentAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendGetRequest(url: '/api/account/00000000-0000-6000-8000-000000000000');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        self::assertErrorResponseSchema();
    }

    public function testTryToGetAccountWithoutPermission(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $accountAdmin = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        self::sendGetRequest(url: '/api/account/' . $accountAdmin->id);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        self::assertErrorResponseSchema();
    }
}
