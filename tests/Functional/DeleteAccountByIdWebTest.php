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
use Tests\Support\HttpClientRequestsTrait;

final class DeleteAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::activateHttpClient();
    }

    public function testEnsureAdminCanDeleteExistingAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountJames = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'james@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountJames);
        self::sendDeleteRequest(url: '/api/account/' . $accountJames->id);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
    }

    public function testTryToDeleteNonexistentAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendDeleteRequest(url: '/api/account/00000000-0000-6000-8000-000000000000');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToDeleteAccountWithoutPermission(): void
    {
        self::loadFixtures([AccountActivatedEmmaFixture::class, AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'emma@example.com', password: 'password4#account');
        $accountJames = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'james@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountJames);
        self::sendDeleteRequest(url: '/api/account/' . $accountJames->id);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
