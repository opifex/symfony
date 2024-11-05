<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\FunctionalTester;

final class SignupNewAccountCest
{
    public function signupUsingNewCredentials(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/auth/signup', params: json_encode([
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]));
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeResponseEquals(expected: '');
    }

    public function signupUsingInvalidCredentials(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/auth/signup', params: json_encode([
            'email' => 'example.com',
            'password' => 'password4#account',
        ]));
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }

    public function signupUsingInvalidTypes(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/auth/signup', params: json_encode([
            'email' => 'example.com',
            'password' => ['password4#account'],
        ]));
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }

    public function signupUsingExistedCredentials(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/auth/signup', params: json_encode([
            'email' => 'admin@example.com',
            'password' => 'password4#account',
        ]));
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }
}