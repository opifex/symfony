<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Persistence\Fixture\AccountFixture;
use Codeception\Attribute\Before;
use Codeception\Util\HttpCode;

final class LifecycleAuthCest
{
    #[Before('prepareHttpHeaders')]
    public function getUserInfoWithInvalidHeader(FunctionalTester $i): void
    {
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);

        $i->haveHttpHeader(name: 'Authorization', value: 'invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);

        $i->haveHttpHeader(name: 'Authorization', value: 'Bearer invalid');
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
    }

    #[Before('prepareHttpHeaders')]
    public function signinWithBadCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'bad@example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signin', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
    }

    #[Before('loadFixtures')]
    #[Before('prepareHttpHeaders')]
    public function signinWithExistedEmail(FunctionalTester $i): void
    {
        $credentials = ['email' => 'user@example.com', 'password' => $i->getDefaultPassword()];

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

    #[Before('prepareHttpHeaders')]
    public function signinWithExtraAttributes(FunctionalTester $i): void
    {
        $credentials = ['email' => 'bad@example.com', 'password' => $i->getDefaultPassword(), 'extra' => 'value'];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
    }

    #[Before('prepareHttpHeaders')]
    public function signupWithBadCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);

        $credentials = ['email' => 'example.com', 'password' => [$i->getDefaultPassword()]];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
    }

    #[Before('loadFixtures')]
    #[Before('prepareHttpHeaders')]
    public function signupWithExistedEmail(FunctionalTester $i): void
    {
        $credentials = ['email' => 'user@example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
    }

    #[Before('prepareHttpHeaders')]
    public function signupWithValidCredentials(FunctionalTester $i): void
    {
        $credentials = ['email' => 'email@example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
    }

    protected function loadFixtures(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
    }

    protected function prepareHttpHeaders(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }
}
