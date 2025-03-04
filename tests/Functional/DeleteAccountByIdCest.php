<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Exception\ModuleException;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\Data\Fixture\AccountUserFixture;
use Tests\Support\FunctionalTester;

final class DeleteAccountByIdCest
{
    /**
     * @throws ModuleException
     */
    public function deleteAccountUsingValidUuid(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendDelete(url: '/api/account/00000000-0000-6000-8001-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseEquals(expected: '');
    }

    /**
     * @throws ModuleException
     */
    public function deleteAccountUsingInvalidUuid(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendDelete(url: '/api/account/00000000-0000-6000-8001-000000000000');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
