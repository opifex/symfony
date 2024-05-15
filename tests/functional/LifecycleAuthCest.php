<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Persistence\Doctrine\Fixture\AccountFixture;
use Codeception\Util\HttpCode;

final class LifecycleAuthCest
{
    public function getUserInfoWithInvalidHeader(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();

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
        $i->haveHttpHeaderApplicationJson();

        $credentials = ['email' => 'bad@example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signin', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
    }

    public function signinWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeaderApplicationJson();

        $credentials = ['email' => 'user@example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signin', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $accessToken = current($i->grabDataFromResponseByJsonPath(jsonPath: '$access_token'));

        $i->haveHttpHeader(name: 'Authorization', value: 'Bearer ' . $accessToken);
        $i->sendGet(url: '/api/auth/me');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => $credentials['email']]);
    }

    public function signinWithExtraAttributes(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();

        $credentials = ['email' => 'bad@example.com', 'password' => $i->getDefaultPassword(), 'extra' => 'value'];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function signupWithBadCredentials(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();

        $credentials = ['email' => 'example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);

        $credentials = ['email' => 'example.com', 'password' => [$i->getDefaultPassword()]];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function signupWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeaderApplicationJson();

        $credentials = ['email' => 'user@example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
    }

    public function signupWithValidCredentials(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();

        $credentials = ['email' => 'email@example.com', 'password' => $i->getDefaultPassword()];

        $i->sendPost(url: '/api/auth/signup', params: json_encode($credentials));
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
    }
}
