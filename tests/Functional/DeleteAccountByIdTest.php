<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedEmmaFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;

final class DeleteAccountByIdTest extends AbstractWebTestCase
{
    public function testEnsureAdminCanDeleteExistingAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');

        /** @var AccountEntity $accountJames */
        $accountJames = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'james@example.com'],
        );

        $this->sendDeleteRequest(url: '/api/account/' . $accountJames->id);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseBodyIsEmpty();
    }

    public function testTryToDeleteNonexistentAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendDeleteRequest(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToDeleteAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedEmmaFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'emma@example.com', password: 'password4#account');

        /** @var AccountEntity $accountJames */
        $accountJames = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'james@example.com'],
        );

        $this->sendDeleteRequest(url: '/api/account/' . $accountJames->id);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
