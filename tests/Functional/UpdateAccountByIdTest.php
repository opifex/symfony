<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Model\LocaleCode;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;

final class UpdateAccountByIdTest extends AbstractWebTestCase
{
    public function testEnsureAdminCanUpdateAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');

        /** @var AccountEntity $accountAdmin */
        $accountAdmin = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'admin@example.com'],
        );

        $this->sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'updated@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseBodyIsEmpty();
    }

    public function testTryToUpdateAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');

        /** @var AccountEntity $accountAdmin */
        $accountAdmin = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'admin@example.com'],
        );

        $this->sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'updated@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToUpdateAccountWithExistedEmail(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');

        /** @var AccountEntity $accountAdmin */
        $accountAdmin = $this->grabEntityFromRepository(
            entity: AccountEntity::class,
            criteria: ['email' => 'admin@example.com'],
        );

        $this->sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'james@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }

    public function testTryToUpdateAccountWithInvalidId(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPatchRequest(url: '/api/account/019661f3-78c3-7a26-9ccf-361042fa4f67', params: [
            'email' => 'user@example.com',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schemaFile: 'ApplicationExceptionSchema.json');
    }
}
