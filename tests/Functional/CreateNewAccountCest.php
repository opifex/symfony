<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminActivatedFixture;
use Tests\Support\Data\Fixture\AccountUserActivatedFixture;
use Tests\Support\FunctionalTester;

final class CreateNewAccountCest
{
    public function createNewUserAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'created@example.com',
                'password' => 'password4#account',
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CREATED);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'CreateNewAccountSchema.json'));
    }

    public function createNewUserAccountWithoutPermission(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');
        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'created@example.com',
                'password' => 'password4#account',
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function createAccountWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'admin@example.com',
                'password' => 'password4#account',
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
