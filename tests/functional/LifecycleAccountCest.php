<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Entity\AccountAction;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Infrastructure\Persistence\Fixture\AccountFixture;
use Codeception\Attribute\Before;

final class LifecycleAccountCest
{
    #[Before('signinWithAdminCredentials')]
    public function actionsWithInvalidAccount(FunctionalTester $i): void
    {
        $i->sendGet(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIsClientError();

        $i->sendPatch(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIsClientError();

        $i->sendDelete(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIsClientError();

        $i->sendPost(url: '/api/account/00000000-0000-6000-8000-000000000000/' . AccountAction::VERIFY);
        $i->seeResponseCodeIsClientError();
    }

    #[Before('signinWithAdminCredentials')]
    public function actionsWithNewAccount(FunctionalTester $i): void
    {
        $newCredentials = [
            'email' => 'created@example.com',
            'password' => 'password4#account',
            'roles' => [AccountRole::ROLE_USER],
        ];
        $updatedCredentials = [
            'email' => 'updated@example.com',
            'password' => 'password4#account',
            'roles' => [AccountRole::ROLE_USER],
        ];

        $searchParams = [
            'email' => $newCredentials['email'],
            'status' => AccountStatus::VERIFIED,
        ];

        $i->sendPost(url: '/api/account', params: json_encode($newCredentials));
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Location');

        $location = $i->grabHttpHeader(name: 'Location');

        $i->sendGet(url: '/api/account', params: $searchParams);
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

    #[Before('signinWithAdminCredentials')]
    public function applyActionToAccount(FunctionalTester $i): void
    {
        $userCredentials = ['email' => 'user@example.com'];

        $i->sendGet(url: '/api/account', params: ['email' => $userCredentials['email']]);
        $i->seeResponseCodeIsSuccessful();
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPost(url: '/api/account/' . $uuid . '/' . AccountAction::VERIFY);
        $i->seeResponseCodeIsClientError();
    }

    #[Before('signinWithAdminCredentials')]
    public function createAccountWithExistedEmail(FunctionalTester $i): void
    {
        $newCredentials = [
            'email' => 'user@example.com',
            'password' => 'password',
            'roles' => [AccountRole::ROLE_USER],
        ];

        $i->sendPost(url: '/api/account', params: json_encode($newCredentials));
        $i->seeResponseCodeIsClientError();
    }

    #[Before('signinWithAdminCredentials')]
    public function updateAccountWithExistedEmail(FunctionalTester $i): void
    {
        $updatedCredentials = ['email' => 'user@example.com'];

        $i->sendGet(url: '/api/account', params: ['email' => 'admin@example.com']);
        $i->seeResponseCodeIsSuccessful();
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPatch(url: '/api/account/' . $uuid, params: json_encode($updatedCredentials));
        $i->seeResponseCodeIsClientError();
    }

    protected function signinWithAdminCredentials(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');
        $i->sendPost(
            url: '/api/auth/signin',
            params: json_encode([
                'email' => 'admin@example.com',
                'password' => 'password4#account',
            ]),
        );
        $i->seeResponseCodeIsSuccessful();
        $i->seeHttpHeader(name: 'Authorization');
        $i->haveHttpHeader(name: 'Authorization', value: $i->grabHttpHeader(name: 'Authorization'));
    }
}
