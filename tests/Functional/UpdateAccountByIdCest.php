<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Data\Fixture\AccountActivatedJamesFixture;
use Tests\Support\FunctionalTester;

final class UpdateAccountByIdCest
{
    public function ensureAdminCanUpdateAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $accountAdminId = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'id',
            params: ['email' => 'admin@example.com'],
        );
        $I->sendPatch(
            url: '/api/account/' . $accountAdminId,
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => 'password4#account',
                'locale' => 'en-US',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseEquals(expected: '');
    }

    public function tryToUpdateAccountWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'james@example.com', password: 'password4#account');
        $accountAdminId = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'id',
            params: ['email' => 'admin@example.com'],
        );
        $I->sendPatch(
            url: '/api/account/' . $accountAdminId,
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => 'password4#account',
                'locale' => 'en-US',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToUpdateAccountWithExistedEmail(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->loadFixtures(fixtures: AccountActivatedJamesFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $accountAdminId = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'id',
            params: ['email' => 'admin@example.com'],
        );
        $I->sendPatch(
            url: '/api/account/' . $accountAdminId,
            params: json_encode([
                'email' => 'james@example.com',
                'password' => 'password4#account',
                'locale' => 'en-US',
            ]),
        );
        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToUpdateAccountWithInvalidId(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendPatch(
            url: '/api/account/019661f3-78c3-7a26-9ccf-361042fa4f67',
            params: json_encode(['email' => 'user@example.com']),
        );
        $I->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
