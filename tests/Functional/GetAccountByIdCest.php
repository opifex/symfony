<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminActivatedFixture;
use Tests\Support\Data\Fixture\AccountUserActivatedFixture;
use Tests\Support\FunctionalTester;

final class GetAccountByIdCest
{
    public function getAccountUsingValidUuid(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'admin@example.com', password: 'password4#account');
        $i->sendGet(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => 'admin@example.com']);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'GetAccountByIdSchema.json'));
    }

    public function getAccountWithoutPermission(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountUserActivatedFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorization(email: 'user@example.com', password: 'password4#account');
        $i->sendGet(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
