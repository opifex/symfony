<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Data\Fixture\AccountActivatedJamesFixture;
use Tests\Support\FunctionalTester;

final class DeleteAccountByIdCest
{
    public function ensureAdminCanDeleteExistingAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendDelete(url: '/api/account/019661ed-775d-7b12-9c57-446490b0165e');
        $I->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseEquals(expected: '');
    }

    public function tryToDeleteNonexistentAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendDelete(url: '/api/account/019661ed-775d-7b12-9c57-446490b0165e');
        $I->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToDeleteAccountWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'james@example.com', password: 'password4#account');
        $I->sendDelete(url: '/api/account/019661ed-775d-7b12-9c57-446490b0165e');
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
