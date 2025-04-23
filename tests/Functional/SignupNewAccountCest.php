<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\FunctionalTester;

final class SignupNewAccountCest
{
    public function ensureUserCanSignup(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signup', params: json_encode([
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]));
        $I->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseEquals(expected: '');
    }

    public function tryToSignupWithInvalidCredentials(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signup', params: json_encode([
            'email' => 'example.com',
            'password' => 'password4#account',
        ]));
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToSignupWithNonexistentCredentials(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signup', params: json_encode([
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]));
        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToSignupWithInvalidTypes(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/auth/signup', params: json_encode([
            'email' => 'example.com',
            'password' => ['password4#account'],
        ]));
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
