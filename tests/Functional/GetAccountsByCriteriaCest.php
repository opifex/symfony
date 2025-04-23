<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\AccountStatus;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminActivatedFixture;
use Tests\Support\Data\Fixture\AccountUserActivatedFixture;
use Tests\Support\FunctionalTester;

final class GetAccountsByCriteriaCest
{
    public function ensureAdminCanGetAccountsByCriteria(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $I->sendGet(
            url: '/api/account',
            params: [
                'email' => 'admin@example.com',
                'status' => AccountStatus::ACTIVATED,
            ],
        );
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['email' => 'admin@example.com']);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'GetAccountsByCriteriaSchema.json'));
    }

    public function tryToGetAccountsByCriteriaWithoutPermission(FunctionalTester $I): void
    {
        $I->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $I->haveHttpHeaderApplicationJson();
        $I->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');
        $I->sendGet(
            url: '/api/account',
            params: [
                'email' => 'admin@example.com',
                'status' => AccountStatus::ACTIVATED,
            ],
        );
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
