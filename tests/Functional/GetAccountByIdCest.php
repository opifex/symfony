<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Data\Fixture\AccountActivatedJamesFixture;
use Tests\Support\FunctionalTester;

final class GetAccountByIdCest
{
    public function ensureAdminCanGetExistingAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $accountAdminId = $I->grabFromRepository(
            entity: AccountEntity::class,
            field: 'id',
            params: ['email' => 'admin@example.com'],
        );
        $I->sendGet(url: '/api/account/' . $accountAdminId);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseContainsJson(['email' => 'admin@example.com']);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'GetAccountByIdSchema.json'));
    }

    public function tryToGetNonexistentAccount(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountActivatedAdminFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendGet(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $I->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToGetAccountWithoutPermission(FunctionalTester $I): void
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
        $I->sendGet(url: '/api/account/' . $accountAdminId);
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
