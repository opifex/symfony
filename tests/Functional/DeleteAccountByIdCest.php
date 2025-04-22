<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminActivatedFixture;
use Tests\Support\Data\Fixture\AccountUserActivatedFixture;
use Tests\Support\FunctionalTester;

final class DeleteAccountByIdCest
{
    public function deleteAccountUsingValidUuid(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $i->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendDelete(url: '/api/account/00000000-0000-6000-8001-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseEquals(expected: '');
    }

    public function deleteAccountWithoutPermission(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');
        $i->sendDelete(url: '/api/account/00000000-0000-6000-8001-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function deleteAccountUsingInvalidUuid(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendDelete(url: '/api/account/00000000-0000-6000-8001-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
