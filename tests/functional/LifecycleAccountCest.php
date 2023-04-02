<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Entity\Account\AccountAction;
use App\Domain\Entity\Account\AccountRole;
use App\Domain\Entity\Account\AccountStatus;
use App\Infrastructure\Persistence\Fixture\AccountFixture;

final class LifecycleAccountCest
{
    public function actionsWithInvalidAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $adminCredentials = ['email' => 'admin@example.com', 'password' => 'password'];
        $invalidUserIdentifier = '00000000-0000-6000-8000-000000000000';

        $i->sendPost(url: '/api/auth/signin', params: json_encode($adminCredentials));
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Authorization');

        $adminAuthToken = $i->grabHttpHeader(name: 'Authorization');

        $i->haveHttpHeader(name: 'Authorization', value: $adminAuthToken);

        $i->sendGet(url: '/api/account/' . $invalidUserIdentifier);
        $i->seeResponseCodeIsClientError();

        $i->sendPatch(url: '/api/account/' . $invalidUserIdentifier);
        $i->seeResponseCodeIsClientError();

        $i->sendDelete(url: '/api/account/' . $invalidUserIdentifier);
        $i->seeResponseCodeIsClientError();

        $i->sendPost(url: '/api/account/' . $invalidUserIdentifier . '/' . AccountAction::VERIFY);
        $i->seeResponseCodeIsClientError();
    }

    public function actionsWithNewAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $adminCredentials = ['email' => 'admin@example.com', 'password' => 'password'];
        $newCredentials = [
            'email' => 'created@example.com',
            'password' => 'password',
            'roles' => [AccountRole::ROLE_USER],
        ];
        $updatedCredentials = ['email' => 'updated@example.com', 'password' => 'password'];

        $i->sendPost(url: '/api/auth/signin', params: json_encode($adminCredentials));
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Authorization');

        $adminAuthToken = $i->grabHttpHeader(name: 'Authorization');

        $i->haveHttpHeader(name: 'Authorization', value: $adminAuthToken);

        $i->sendPost(url: '/api/account', params: json_encode($newCredentials));
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Location');

        $location = $i->grabHttpHeader(name: 'Location');

        $i->sendGet(url: '/api/account', params: ['email' => $newCredentials['email']]);
        $i->seeResponseCodeIsSuccessful();
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPatch(url: $location, params: json_encode($updatedCredentials));
        $i->seeResponseCodeIsSuccessful();

        $i->sendPost(url: $location . '/' . AccountAction::BLOCK);
        $i->seeResponseCodeIsSuccessful();

        $i->sendGet(url: $location);
        $i->seeResponseCodeIsSuccessful();
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(
            [
                'uuid' => $uuid,
                'email' => $updatedCredentials['email'],
                'status' => AccountStatus::BLOCKED,
            ],
        );

        $i->sendDelete(url: $location);
        $i->seeResponseCodeIsSuccessful();
    }

    public function applyActionToAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $adminCredentials = ['email' => 'admin@example.com', 'password' => 'password'];
        $userCredentials = ['email' => 'user@example.com'];

        $i->sendPost(url: '/api/auth/signin', params: json_encode($adminCredentials));
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Authorization');

        $adminAuthToken = $i->grabHttpHeader(name: 'Authorization');

        $i->haveHttpHeader(name: 'Authorization', value: $adminAuthToken);

        $i->sendGet(url: '/api/account', params: ['email' => $userCredentials['email']]);
        $i->seeResponseCodeIsSuccessful();
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPost(url: '/api/account/' . $uuid . '/' . AccountAction::VERIFY);
        $i->seeResponseCodeIsClientError();
    }

    public function createAccountWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $adminCredentials = ['email' => 'admin@example.com', 'password' => 'password'];
        $newCredentials = [
            'email' => 'user@example.com',
            'password' => 'password',
            'roles' => [AccountRole::ROLE_USER],
        ];

        $i->sendPost(url: '/api/auth/signin', params: json_encode($adminCredentials));
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Authorization');

        $adminAuthToken = $i->grabHttpHeader(name: 'Authorization');

        $i->haveHttpHeader(name: 'Authorization', value: $adminAuthToken);

        $i->sendPost(url: '/api/account', params: json_encode($newCredentials));
        $i->seeResponseCodeIsClientError();
    }

    public function updateAccountWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);

        $adminCredentials = ['email' => 'admin@example.com', 'password' => 'password'];
        $updatedCredentials = ['email' => 'user@example.com'];

        $i->sendPost(url: '/api/auth/signin', params: json_encode($adminCredentials));
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Authorization');

        $adminAuthToken = $i->grabHttpHeader(name: 'Authorization');

        $i->haveHttpHeader(name: 'Authorization', value: $adminAuthToken);

        $i->sendGet(url: '/api/account', params: ['email' => $adminCredentials['email']]);
        $i->seeResponseCodeIsSuccessful();
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPatch(url: '/api/account/' . $uuid, params: json_encode($updatedCredentials));
        $i->seeResponseCodeIsClientError();
    }
}
