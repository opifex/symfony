<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Data\Fixture\AccountActivatedJamesFixture;
use Tests\Support\Data\Fixture\AccountBlockedHenryFixture;
use Tests\Support\FunctionalTester;

final class UnblockAccountByIdCest
{
    public function ensureAdminCanUnblockBlockedAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->loadFixtures(fixtures: AccountBlockedHenryFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $accountHenryId = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'id',
            params: ['email' => 'henry@example.com'],
        );
        $I->sendPost(url: '/api/account/' . $accountHenryId . '/unblock');
        $I->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseEquals(expected: '');
    }

    public function tryToUnblockNonexistentAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendPost(url: '/api/account/00000000-0000-6000-8000-000000000000/unblock');
        $I->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToUnblockBlockedAccountWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->loadFixtures(fixtures: AccountBlockedHenryFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'james@example.com', password: 'password4#account');
        $accountHenryId = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'id',
            params: ['email' => 'henry@example.com'],
        );
        $I->sendPost(url: '/api/account/' . $accountHenryId . '/unblock');
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
