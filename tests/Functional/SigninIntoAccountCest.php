<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Data\Fixture\AccountRegisteredOliviaFixture;
use Tests\Support\FunctionalTester;

final class SigninIntoAccountCest
{
    public function ensureAdminCanSignin(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]));
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'SigninIntoAccountSchema.json'));
    }

    public function tryToSigninWithNonactivatedUser(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountRegisteredOliviaFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'olivia@example.com',
            'password' => 'password4#account',
        ]));
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToSigninWithInvalidCredentials(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'invalid@example.com',
            'password' => 'password4#account',
        ]));
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToSigninWithInvalidJson(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signin', params: '[...]');
        $I->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToSigninWithExtraAttributes(FunctionalTester $I): void
    {
        $I->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'extra' => 'value',
        ]));
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
