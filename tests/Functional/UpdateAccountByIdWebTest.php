<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Localization\LocaleCode;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\HttpClientRequestsTrait;

final class UpdateAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::activateHttpClient();
    }

    public function testEnsureAdminCanUpdateAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountAdmin = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        self::sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'updated@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        self::assertResponseContentSame(expectedContent: '');
    }

    public function testTryToUpdateAccountWithoutPermission(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $accountAdmin = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        self::sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'updated@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToUpdateAccountWithExistedEmail(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountAdmin = self::getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        self::assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        self::sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'james@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToUpdateAccountWithInvalidId(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendPatchRequest(url: '/api/account/019661f3-78c3-7a26-9ccf-361042fa4f67', params: [
            'email' => 'user@example.com',
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
