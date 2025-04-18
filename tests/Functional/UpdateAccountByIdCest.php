<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\Data\Fixture\AccountUserFixture;
use Tests\Support\FunctionalTester;

final class UpdateAccountByIdCest
{
    public function updateAccountInfo(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendPatch(
            url: '/api/account/00000000-0000-6000-8000-000000000000',
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => 'password4#account',
                'locale' => 'en_US',
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseEquals(expected: '');
    }

    public function updateAccountInfoWithoutPermission(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');
        $i->sendPatch(
            url: '/api/account/00000000-0000-6000-8000-000000000000',
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => 'password4#account',
                'locale' => 'en_US',
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function updateAccountUsingExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendPatch(
            url: '/api/account/00000000-0000-6000-8000-000000000000',
            params: json_encode([
                'email' => 'user@example.com',
                'password' => 'password4#account',
                'locale' => 'en_US',
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function updateEmailUsingInvalidUuid(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendPatch(
            url: '/api/account/00000000-0000-6000-8001-000000000000',
            params: json_encode(['email' => 'user@example.com']),
        );
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
