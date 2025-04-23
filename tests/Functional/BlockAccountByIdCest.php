<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Data\Fixture\AccountActivatedEmmaFixture;
use Tests\Support\Data\Fixture\AccountActivatedJamesFixture;
use Tests\Support\Data\Fixture\AccountBlockedHenryFixture;
use Tests\Support\FunctionalTester;

final class BlockAccountByIdCest
{
    public function ensureAdminCanBlockActivatedAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');

        $I->sendPost(url: '/api/account/019661ed-775d-7b12-9c57-446490b0165e/block');
        $I->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseEquals(expected: '');
    }

    public function tryToBlockAlreadyBlockedAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->loadFixtures(fixtures: AccountBlockedHenryFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');

        $I->sendPost(url: '/api/account/019661ee-9ecf-79eb-9ba1-f211f1975995/block');
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToBlockAccountWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedEmmaFixture::class);
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'james@example.com', password: 'password4#account');

        $I->sendPost(url: '/api/account/019661ee-9ecf-79eb-9ba1-f211f1975995/block');
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
