<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\AccountStatus;
use Codeception\Util\HttpCode;
use Exception;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\FunctionalTester;

final class GetAccountsByCriteriaCest
{
    /**
     * @throws Exception
     */
    public function getAccountsByCriteria(FunctionalTester $i): void
    {
        $i->loadFixtures(fixtures: AccountAdminFixture::class);
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeaderAuthorizationAdmin(email: 'admin@example.com', password: 'password4#account');
        $i->sendGet(
            url: '/api/account',
            params: [
                'email' => 'admin@example.com',
                'status' => AccountStatus::ACTIVATED,
            ],
        );
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => 'admin@example.com']);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(schema: 'GetAccountsByCriteriaResponse.json'));
    }
}
