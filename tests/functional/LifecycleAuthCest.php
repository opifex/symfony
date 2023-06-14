<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Persistence\Fixture\AccountFixture;

final class LifecycleAuthCest
{
    public function getUserInfoWithInvalidHeader(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIsClientError();

        $i->haveHttpHeader(name: 'Authorization', value: 'invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIsClientError();

        $i->haveHttpHeader(name: 'Authorization', value: 'Bearer invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIsClientError();
    }

    public function signinWithBadCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'bad@example.com', 'password' => 'password'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signin', params: json_encode($credentials));
        $i->seeResponseCodeIsClientError();
    }

    public function signinWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $credentials = ['email' => 'user@example.com', 'password' => 'password'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signin', params: json_encode($credentials));
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Authorization');

        $userAuthToken = $i->grabHttpHeader(name: 'Authorization');

        $i->haveHttpHeader(name: 'Authorization', value: $userAuthToken);
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIsSuccessful();
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => $credentials['email']]);
    }

    public function signinWithExtraAttributes(FunctionalTester $i): void
    {
        $credentials = ['email' => 'bad@example.com', 'password' => 'password', 'extra' => 'value'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIsClientError();
    }

    public function signupWithBadCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'example.com', 'password' => 'password'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIsClientError();

        $credentials = ['email' => 'example.com', 'password' => ['password']];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIsClientError();
    }

    public function signupWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $credentials = ['email' => 'user@example.com', 'password' => 'password'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIsClientError();
    }

    public function signupWithValidCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'email@example.com', 'password' => 'password'];

        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIsSuccessful();
    }
}
