<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Data\Fixture\AccountActivatedEmmaFixture;
use Tests\Support\Data\Fixture\AccountActivatedJamesFixture;
use Tests\Support\Data\Fixture\AccountBlockedHenryFixture;
use Tests\Support\FunctionalTester;

final class BlockAccountByIdCest
{
    public function ensureAdminCanBlockActivatedAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $accountJamesUuid = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'uuid',
            params: ['email' => 'james@example.com'],
        );
        $I->sendPost(url: '/api/account/' . $accountJamesUuid . '/block');
        $I->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseEquals(expected: '');
    }

    public function tryToBlockAlreadyBlockedAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->loadFixtures(fixtures: AccountBlockedHenryFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $accountHenryUuid = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'uuid',
            params: ['email' => 'henry@example.com'],
        );
        $I->sendPost(url: '/api/account/' . $accountHenryUuid . '/block');
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToBlockAccountWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedEmmaFixture::class);
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'james@example.com', password: 'password4#account');
        $accountEmmaUuid = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'uuid',
            params: ['email' => 'emma@example.com'],
        );
        $I->sendPost(url: '/api/account/' . $accountEmmaUuid . '/block');
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
