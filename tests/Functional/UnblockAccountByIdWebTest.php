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
use Tests\Support\Fixture\AccountBlockedHenryFixture;
use Tests\Support\HttpClientRequestsTrait;

final class UnblockAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testEnsureAdminCanUnblockBlockedAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountBlockedHenryFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountHenry = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'henry@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountHenry);
        $this->sendPostRequest(url: '/api/account/' . $accountHenry->id . '/unblock');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseContentSame(expectedContent: '');
    }

    public function testTryToUnblockNonexistentAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account/00000000-0000-6000-8000-000000000000/unblock');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToUnblockBlockedAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedJamesFixture::class, AccountBlockedHenryFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $accountHenry = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'henry@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountHenry);
        $this->sendPostRequest(url: '/api/account/' . $accountHenry->id . '/unblock');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
