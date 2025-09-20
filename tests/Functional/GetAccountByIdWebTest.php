<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\HttpClientComponentTrait;

final class GetAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientComponentTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testEnsureAdminCanGetExistingAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountAdmin = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        $this->sendGetRequest(url: '/api/account/' . $accountAdmin->id);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schema: 'GetAccountByIdSchema.json');
    }

    public function testTryToGetNonexistentAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendGetRequest(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToGetAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $accountAdmin = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        $this->sendGetRequest(url: '/api/account/' . $accountAdmin->id);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
