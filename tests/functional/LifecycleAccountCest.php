<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Entity\AccountAction;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Infrastructure\Persistence\Fixture\AccountFixture;
use Codeception\Attribute\Before;
use Codeception\Util\HttpCode;

final class LifecycleAccountCest
{
    #[Before('loadFixtures')]
    #[Before('prepareHttpHeaders')]
    #[Before('signinWithAdminCredentials')]
    public function actionsWithInvalidAccount(FunctionalTester $i): void
    {
        $i->sendGet(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);

        $i->sendPatch(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);

        $i->sendDelete(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);

        $i->sendPost(url: '/api/account/00000000-0000-6000-8000-000000000000/' . AccountAction::VERIFY);
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
    }

    #[Before('loadFixtures')]
    #[Before('prepareHttpHeaders')]
    #[Before('signinWithAdminCredentials')]
    public function actionsWithNewAccount(FunctionalTester $i): void
    {
        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'created@example.com',
                'password' => 'password4#account',
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeHttpHeader(name: 'Location');

        $location = $i->grabHttpHeader(name: 'Location');

        $i->sendGet(
            url: '/api/account',
            params: [
                'email' => 'created@example.com',
                'status' => AccountStatus::VERIFIED,
            ],
        );
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPatch(
            url: $location,
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => 'password4#account',
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);

        $i->sendPost(url: $location . '/' . AccountAction::BLOCK);
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);

        $i->sendGet(url: $location);
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(
            [
                'uuid' => $uuid,
                'email' => 'updated@example.com',
                'status' => AccountStatus::BLOCKED,
            ],
        );

        $i->sendDelete(url: $location);
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
    }

    #[Before('loadFixtures')]
    #[Before('prepareHttpHeaders')]
    #[Before('signinWithAdminCredentials')]
    public function applyActionToAccount(FunctionalTester $i): void
    {
        $i->sendGet(url: '/api/account', params: ['email' => 'user@example.com']);
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPost(url: '/api/account/' . $uuid . '/' . AccountAction::VERIFY);
        $i->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
    }

    #[Before('loadFixtures')]
    #[Before('prepareHttpHeaders')]
    #[Before('signinWithAdminCredentials')]
    public function createAccountWithExistedEmail(FunctionalTester $i): void
    {
        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'user@example.com',
                'password' => 'password4#account',
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
    }

    #[Before('loadFixtures')]
    #[Before('prepareHttpHeaders')]
    #[Before('signinWithAdminCredentials')]
    public function updateAccountWithExistedEmail(FunctionalTester $i): void
    {
        $i->sendGet(url: '/api/account', params: ['email' => 'admin@example.com']);
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPatch(url: '/api/account/' . $uuid, params: json_encode(['email' => 'user@example.com']));
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
    }

    protected function loadFixtures(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
    }

    protected function prepareHttpHeaders(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }

    protected function signinWithAdminCredentials(FunctionalTester $i): void
    {
        $i->sendPost(
            url: '/api/auth/signin',
            params: json_encode([
                'email' => 'admin@example.com',
                'password' => 'password4#account',
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeHttpHeader(name: 'Authorization');

        $authorizationHeader = $i->grabHttpHeader(name: 'Authorization');

        $i->haveHttpHeader(name: 'Authorization', value: $authorizationHeader);
    }
}
