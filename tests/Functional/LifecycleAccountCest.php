<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\AccountAction;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Infrastructure\Persistence\Doctrine\Fixture\AccountFixture;
use Codeception\Util\HttpCode;
use Tests\Support\FunctionalTester;

final class LifecycleAccountCest
{
    public function actionsWithInvalidAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin();

        $i->sendGet(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);

        $i->sendPatch(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);

        $i->sendDelete(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);

        $i->sendPost(url: '/api/account/00000000-0000-6000-8000-000000000000/' . AccountAction::ACTIVATE);
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
    }

    public function invalidActionsWithUserAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeaderApplicationJson();

        $i->sendPost(
            url: '/api/auth/signin',
            params: json_encode([
                'email' => 'user@example.com',
                'password' => $i->getDefaultPassword(),
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $accessToken = current($i->grabDataFromResponseByJsonPath(jsonPath: '$access_token'));

        $i->haveHttpHeader(name: 'Authorization', value: 'Bearer ' . $accessToken);

        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'created@example.com',
                'password' => $i->getDefaultPassword(),
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);

        $i->sendGet(url: '/api/account');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);

        $i->sendGet(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);

        $i->sendPatch(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);

        $i->sendDelete(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);

        $i->sendPost(url: '/api/account/00000000-0000-6000-8000-000000000000/' . AccountAction::ACTIVATE);
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
    }

    public function actionsWithNewAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin();

        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'created@example.com',
                'password' => $i->getDefaultPassword(),
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CREATED);

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$uuid'));

        $i->sendGet(
            url: '/api/account',
            params: [
                'email' => 'created@example.com',
                'status' => AccountStatus::CREATED,
            ],
        );
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $i->sendPost(url: '/api/account/' . $uuid . '/' . AccountAction::ACTIVATE);
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);

        $i->sendGet(
            url: '/api/account',
            params: [
                'email' => 'created@example.com',
                'status' => AccountStatus::ACTIVATED,
            ],
        );
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPatch(
            url: '/api/account/' . $uuid,
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => $i->getDefaultPassword(),
                'locale' => 'en_US',
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);

        $i->sendPost(url: '/api/account/' . $uuid . '/' . AccountAction::BLOCK);
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);

        $i->sendGet(url: '/api/account/' . $uuid);
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(
            [
                'uuid' => $uuid,
                'email' => 'updated@example.com',
                'status' => AccountStatus::BLOCKED,
            ],
        );

        $i->sendDelete(url: '/api/account/' . $uuid);
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
    }

    public function applyActionToAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin();

        $i->sendGet(url: '/api/account', params: ['email' => 'user@example.com']);
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPost(url: '/api/account/' . $uuid . '/' . AccountAction::ACTIVATE);
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function createAccountWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin();

        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'user@example.com',
                'password' => $i->getDefaultPassword(),
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
    }

    public function updateAccountWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin();

        $i->sendGet(url: '/api/account', params: ['email' => 'admin@example.com']);
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();

        $uuid = current($i->grabDataFromResponseByJsonPath(jsonPath: '$[0].uuid'));

        $i->sendPatch(url: '/api/account/' . $uuid, params: json_encode(['email' => 'user@example.com']));
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
    }
}
