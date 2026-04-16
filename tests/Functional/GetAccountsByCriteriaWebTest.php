<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Account\AccountStatus;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\HttpClientRequestsTrait;

final class GetAccountsByCriteriaWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::loadHttpClient();
    }

    public function testEnsureAdminCanGetAccountsByCriteria(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendGetRequest(url: '/api/account', params: [
            'email' => 'admin@example.com',
            'status' => AccountStatus::Activated->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertResponseSchema();
    }

    public function testEryToGetAccountsByCriteriaWithoutPermission(): void
    {
        self::loadFixtures([AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        self::sendGetRequest(url: '/api/account', params: [
            'email' => 'admin@example.com',
            'status' => AccountStatus::Activated->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        self::assertErrorResponseSchema();
    }
}
