<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminActivatedFixture;
use Tests\Support\Data\Fixture\AccountUserActivatedFixture;
use Tests\Support\FunctionalTester;

final class UpdateAccountByIdCest
{
    public function updateAccountInfo(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendPatch(
            url: '/api/account/00000000-0000-6000-8000-000000000000',
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => 'password4#account',
                'locale' => 'en_US',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseEquals(expected: '');
    }

    public function updateAccountInfoWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');
        $I->sendPatch(
            url: '/api/account/00000000-0000-6000-8000-000000000000',
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => 'password4#account',
                'locale' => 'en_US',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function updateAccountUsingExistedEmail(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $I->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendPatch(
            url: '/api/account/00000000-0000-6000-8000-000000000000',
            params: json_encode([
                'email' => 'user@example.com',
                'password' => 'password4#account',
                'locale' => 'en_US',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function updateEmailUsingInvalidUuid(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendPatch(
            url: '/api/account/00000000-0000-6000-8001-000000000000',
            params: json_encode(['email' => 'user@example.com']),
        );
        $I->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
