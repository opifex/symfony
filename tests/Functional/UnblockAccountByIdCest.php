<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\Data\Fixture\AccountUserFixture;
use Tests\Support\FunctionalTester;

final class UnblockAccountByIdCest
{
    public function applyUnlockAccountAction(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/block');
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseEquals(expected: '');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/unblock');
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseEquals(expected: '');
    }

    public function applyUnblockAccountActionWithoutPermission(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/unblock');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
