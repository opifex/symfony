<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\AccountAction;
use Codeception\Util\HttpCode;
use Exception;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\Data\Fixture\AccountUserFixture;
use Tests\Support\FunctionalTester;

final class ApplyAccountActionCest
{
    /**
     * @throws Exception
     */
    public function applyAccountActivateAction(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/' . AccountAction::ACTIVATE);
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeResponseEquals(expected: '');
    }

    /**
     * @throws Exception
     */
    public function applyAccountRegisterAction(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendPost(url: '/api/account/00000000-0000-6000-8001-000000000000/' . AccountAction::REGISTER);
        $i->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }
}
