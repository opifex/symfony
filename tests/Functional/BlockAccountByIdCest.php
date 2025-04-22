<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminActivatedFixture;
use Tests\Support\Data\Fixture\AccountUserActivatedFixture;
use Tests\Support\Data\Fixture\AccountUserBlockedFixture;
use Tests\Support\FunctionalTester;

final class BlockAccountByIdCest
{
    public function applyBlockAccountAction(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $I->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');

        $I->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/block');
        $I->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseEquals(expected: '');
    }

    public function applyBlockAccountActionOnBlockedAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $I->loadFixtures(fixtures: AccountUserBlockedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');

        $I->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/block');
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function applyBlockAccountActionWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');

        $I->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/block');
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
