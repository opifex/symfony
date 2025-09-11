<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;

final class GetAccountByIdTest extends AbstractWebTestCase
{
    public function testEnsureAdminCanGetExistingAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');

        /** @var AccountEntity $accountAdmin */
        $accountAdmin = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'admin@example.com'],
        );

        $this->sendGetRequest(url: '/api/account/' . $accountAdmin->id);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schemaFile: 'GetAccountByIdSchema.json');
    }

    public function testTryToGetNonexistentAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendGetRequest(url: '/api/account/00000000-0000-6000-8000-000000000000');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToGetAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');

        /** @var AccountEntity $accountAdmin */
        $accountAdmin = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'admin@example.com'],
        );

        $this->sendGetRequest(url: '/api/account/' . $accountAdmin->id);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
