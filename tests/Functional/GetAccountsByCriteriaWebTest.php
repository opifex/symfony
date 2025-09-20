<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Model\AccountStatus;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\HttpClientComponentTrait;

final class GetAccountsByCriteriaWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientComponentTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testEnsureAdminCanGetAccountsByCriteria(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendGetRequest(url: '/api/account', params: [
            'email' => 'admin@example.com',
            'status' => AccountStatus::Activated->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schema: 'GetAccountsByCriteriaSchema.json');
    }

    public function testEryToGetAccountsByCriteriaWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $this->sendGetRequest(url: '/api/account', params: [
            'email' => 'admin@example.com',
            'status' => AccountStatus::Activated->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
