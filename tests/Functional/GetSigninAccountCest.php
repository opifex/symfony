<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminActivatedFixture;
use Tests\Support\FunctionalTester;

final class GetSigninAccountCest
{
    public function ensureUserCanGetSigninAccountWithValidBearer(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendGet(url: '/api/auth/me');
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['email' => 'admin@example.com']);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'GetSigninAccountSchema.json'));
    }

    public function tryToGetSigninAccountWithoutAuthorizationHeader(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendGet(url: '/api/auth/me');
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToGetSigninAccountWithInvalidAuthorizationHeader(FunctionalTester $I): void
    {
        $I->haveHttpHeader(name: 'Authorization', value: 'invalid');
        $I->sendGet(url: '/api/auth/me');
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToGetSigninAccountWithInvalidBearerToken(FunctionalTester $I): void
    {
        $I->haveHttpHeader(name: 'Authorization', value: 'Bearer invalid');
        $I->sendGet(url: '/api/auth/me');
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
