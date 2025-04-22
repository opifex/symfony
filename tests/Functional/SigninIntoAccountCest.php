<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminActivatedFixture;
use Tests\Support\Data\Fixture\AccountUserActivatedFixture;
use Tests\Support\FunctionalTester;

final class SigninIntoAccountCest
{
    public function signinUsingAdminCredentials(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
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

    public function signinUsingRegisteredCredentials(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'registered@example.com',
            'password' => 'password4#account',
        ]));
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function signinUsingInvalidCredentials(FunctionalTester $I): void
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

    public function signinUsingInvalidJson(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signin', params: '[...]');
        $I->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function signinUsingExtraAttributes(FunctionalTester $I): void
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
