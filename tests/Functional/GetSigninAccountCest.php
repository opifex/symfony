<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Exception;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\FunctionalTester;

final class GetSigninAccountCest
{
    /**
     * @throws Exception
     */
    public function getSigninAccountUsingValidBearer(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => 'admin@example.com']);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'GetSigninAccountResponse.json'));
    }

    public function getSigninAccountWithoutAuthorizationHeader(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }

    public function getSigninAccountUsingInvalidHeader(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Authorization', value: 'invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }

    public function getSigninAccountUsingInvalidBearer(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Authorization', value: 'Bearer invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }
}
