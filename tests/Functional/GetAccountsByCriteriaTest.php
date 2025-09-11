<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Model\AccountStatus;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;

final class GetAccountsByCriteriaTest extends AbstractWebTestCase
{
    public function testEnsureAdminCanGetAccountsByCriteria(): void
    {
        $this->loadFixture([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendGetRequest(url: '/api/account', parameters: [
            'email' => 'admin@example.com',
            'status' => AccountStatus::Activated->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schemaFile: 'GetAccountsByCriteriaSchema.json');
        $response = $this->grabArrayFromResponse();
        $this->assertArrayHasKey(key: 'email', array: $response['data'][0]);
        $this->assertEquals(expected: 'admin@example.com', actual: $response['data'][0]['email']);
    }

    public function testEryToGetAccountsByCriteriaWithoutPermission(): void
    {
        $this->loadFixture([AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $this->sendGetRequest(url: '/api/account', parameters: [
            'email' => 'admin@example.com',
            'status' => AccountStatus::Activated->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
