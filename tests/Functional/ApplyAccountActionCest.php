<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Exception\ModuleException;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\Data\Fixture\AccountUserFixture;
use Tests\Support\FunctionalTester;

final class ApplyAccountActionCest
{
    /**
     * @throws ModuleException
     */
    public function applyValidAccountAction(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/activate');
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseEquals(expected: '');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/block');
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseEquals(expected: '');

        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/unblock');
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseEquals(expected: '');
    }

    /**
     * @throws ModuleException
     */
    public function applyInvalidAccountAction(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/unblock');
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
