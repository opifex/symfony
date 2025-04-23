<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Data\Fixture\AccountActivatedJamesFixture;
use Tests\Support\FunctionalTester;

final class CreateNewAccountCest
{
    public function ensureAdminCanCreateAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'created@example.com',
                'password' => 'password4#account',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::CREATED);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'CreateNewAccountSchema.json'));
    }

    public function tryToCreateAccountWithExistingEmail(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'admin@example.com',
                'password' => 'password4#account',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToCreateAccountWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'james@example.com', password: 'password4#account');
        $I->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'created@example.com',
                'password' => 'password4#account',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
