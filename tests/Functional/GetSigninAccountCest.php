<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\FunctionalTester;

final class GetSigninAccountCest
{
    public function getSigninAccountUsingValidBearer(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => 'admin@example.com']);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'GetSigninAccountSchema.json'));
    }

    public function getSigninAccountWithoutAuthorizationHeader(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function getSigninAccountUsingInvalidHeader(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Authorization', value: 'invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function getSigninAccountUsingInvalidBearer(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Authorization', value: 'Bearer invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
