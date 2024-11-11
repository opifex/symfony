<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\FunctionalTester;

final class GetAccountByIdCest
{
    public function getAccountUsingValidUuid(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendGet(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => 'admin@example.com']);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'GetAccountByIdSchema.json'));
    }
}
