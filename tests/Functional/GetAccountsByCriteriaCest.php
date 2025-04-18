<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\AccountStatus;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\Data\Fixture\AccountUserFixture;
use Tests\Support\FunctionalTester;

final class GetAccountsByCriteriaCest
{
    public function getAccountsByCriteria(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendGet(
            url: '/api/account',
            params: [
                'email' => 'admin@example.com',
                'status' => AccountStatus::ACTIVATED,
            ],
        );
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => 'admin@example.com']);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'GetAccountsByCriteriaSchema.json'));
    }

    public function getAccountsByCriteriaWithoutPermission(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');
        $i->sendGet(
            url: '/api/account',
            params: [
                'email' => 'admin@example.com',
                'status' => AccountStatus::ACTIVATED,
            ],
        );
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
