<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Exception\ModuleException;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\Data\Fixture\AccountUserFixture;
use Tests\Support\FunctionalTester;

final class SigninIntoAccountCest
{
    /**
     * @throws ModuleException
     */
    public function signinUsingAdminCredentials(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]));
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsJson();
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'SigninIntoAccountSchema.json'));
    }

    /**
     * @throws ModuleException
     */
    public function signinUsingRegisteredCredentials(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'registered@example.com',
            'password' => 'password4#account',
        ]));
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsJson();
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function signinUsingInvalidCredentials(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'invalid@example.com',
            'password' => 'password4#account',
        ]));
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsJson();
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function signinUsingInvalidJson(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/auth/signin', params: '[...]');
        $i->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsJson();
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function signinUsingExtraAttributes(FunctionalTester $i): void
    {
        $i->sendPost(url: '/api/auth/signin', params: json_encode([
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'extra' => 'value',
        ]));
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
