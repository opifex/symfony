<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\AccountRole;
use Codeception\Util\HttpCode;
use Exception;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\Data\Fixture\AccountUserFixture;
use Tests\Support\FunctionalTester;

final class UpdateAccountByIdCest
{
    /**
     * @throws Exception
     */
    public function updateAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendPatch(
            url: '/api/account/00000000-0000-6000-8000-000000000000',
            params: json_encode([
                'email' => 'updated@example.com',
                'password' => 'password4#account',
                'locale' => 'en_US',
                'roles' => [AccountRole::ROLE_ADMIN],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $i->seeResponseEquals(expected: '');
    }

    /**
     * @throws Exception
     */
    public function updateAccountUsingExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->loadFixtures(fixtures: AccountUserFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendPatch(
            url: '/api/account/00000000-0000-6000-8000-000000000000',
            params: json_encode([
                'email' => 'user@example.com',
                'password' => 'password4#account',
                'locale' => 'en_US',
                'roles' => [AccountRole::ROLE_ADMIN],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }

    /**
     * @throws Exception
     */
    public function updateEmailUsingInvalidUuid(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendPatch(
            url: '/api/account/00000000-0000-6000-8001-000000000000',
            params: json_encode(['email' => 'user@example.com']),
        );
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }
}
