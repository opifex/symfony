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
    public function applyBlockAccountAction(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $i->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/block');
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseEquals(expected: '');
    }

    public function applyBlockAccountActionOnBlockedAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $i->loadFixtures(fixtures: AccountUserBlockedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/block');
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function applyBlockAccountActionWithoutPermission(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/block');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
