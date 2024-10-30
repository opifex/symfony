<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\AccountRole;
use Codeception\Util\HttpCode;
use Exception;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\FunctionalTester;

final class CreateNewAccountCest
{
    /**
     * @throws Exception
     */
    public function createNewAccount(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'created@example.com',
                'password' => 'password4#account',
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CREATED);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'CreateNewAccountResponse.json'));
    }

    /**
     * @throws Exception
     */
    public function createAccountWithExistedEmail(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendPost(
            url: '/api/account',
            params: json_encode([
                'email' => 'admin@example.com',
                'password' => 'password4#account',
                'roles' => [AccountRole::ROLE_USER],
            ]),
        );
        $i->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'ApplicationExceptionResponse.json'));
    }
}
