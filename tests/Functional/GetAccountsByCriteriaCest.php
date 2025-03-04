<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\AccountStatus;
use Codeception\Exception\ModuleException;
use Codeception\Util\HttpCode;
use Tests\Support\Data\Fixture\AccountAdminFixture;
use Tests\Support\FunctionalTester;

final class GetAccountsByCriteriaCest
{
    /**
     * @throws ModuleException
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
                'status' => AccountStatus::Activated->value,
            ],
        );
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 200);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['email' => 'admin@example.com']);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'GetAccountsByCriteriaSchema.json'));
    }
}
