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
use Tests\Support\Fixture\AccountBlockedHenryFixture;
use Tests\Support\HttpClientComponentTrait;

final class BlockAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientComponentTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testEnsureAdminCanBlockActivatedAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountJames = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'james@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountJames);
        $this->sendPostRequest(url: '/api/account/' . $accountJames->id . '/block');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseContentSame(expectedContent: '');
    }

    public function testTryToBlockNonexistentAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPostRequest(url: '/api/account/00000000-0000-6000-8000-000000000000/block');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToBlockAlreadyBlockedAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountBlockedHenryFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountHenry = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'henry@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountHenry);
        $this->sendPostRequest(url: '/api/account/' . $accountHenry->id . '/block');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToBlockAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedEmmaFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $accountEmma = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'emma@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountEmma);
        $this->sendPostRequest(url: '/api/account/' . $accountEmma->id . '/block');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
