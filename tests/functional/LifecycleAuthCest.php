<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Persistence\Fixture\AccountFixture;
use Codeception\Util\HttpCode;

final class LifecycleAuthCest
{
    public function getUserInfoWithInvalidHeader(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);

        $i->haveHttpHeader(name: 'Authorization', value: 'invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);

        $i->haveHttpHeader(name: 'Authorization', value: 'Bearer invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
    }

    public function signinWithBadCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'bad@example.com', 'password' => 'password4#account'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signin', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
    }

    public function signinWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $credentials = ['email' => 'user@example.com', 'password' => 'password4#account'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signin', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeHttpHeader(name: 'Authorization');

        $userAuthToken = $i->grabHttpHeader(name: 'Authorization');

        $i->haveHttpHeader(name: 'Authorization', value: $userAuthToken);
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => $credentials['email']]);
    }

    public function signinWithExtraAttributes(FunctionalTester $i): void
    {
        $credentials = ['email' => 'bad@example.com', 'password' => 'password4#account', 'extra' => 'value'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
    }

    public function signupWithBadCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'example.com', 'password' => 'password4#account'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);

        $credentials = ['email' => 'example.com', 'password' => ['password4#account']];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
    }

    public function signupWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $credentials = ['email' => 'user@example.com', 'password' => 'password4#account'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
    }

    public function signupWithValidCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'email@example.com', 'password' => 'password4#account'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
    }
}
