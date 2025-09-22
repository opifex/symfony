<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedEmmaFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\HttpClientRequestsTrait;

final class DeleteAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testEnsureAdminCanDeleteExistingAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountJames = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'james@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountJames);
        $this->sendDeleteRequest(url: '/api/account/' . $accountJames->id);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseContentSame(expectedContent: '');
    }

    public function testTryToDeleteNonexistentAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendDeleteRequest(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToDeleteAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedEmmaFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'emma@example.com', password: 'password4#account');
        $accountJames = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'james@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountJames);
        $this->sendDeleteRequest(url: '/api/account/' . $accountJames->id);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
