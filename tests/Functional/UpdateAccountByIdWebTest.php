<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Common\LocaleCode;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\HttpClientRequestsTrait;

final class UpdateAccountByIdWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testEnsureAdminCanUpdateAccount(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountAdmin = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        $this->sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'updated@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        $this->assertResponseContentSame(expectedContent: '');
    }

    public function testTryToUpdateAccountWithoutPermission(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        $accountAdmin = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        $this->sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'updated@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToUpdateAccountWithExistedEmail(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class, AccountActivatedJamesFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $accountAdmin = $this->getDatabaseEntity(entity: AccountEntity::class, criteria: [
            'email' => 'admin@example.com',
        ]);
        $this->assertInstanceOf(expected: AccountEntity::class, actual: $accountAdmin);
        $this->sendPatchRequest(url: '/api/account/' . $accountAdmin->id, params: [
            'email' => 'james@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToUpdateAccountWithInvalidId(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendPatchRequest(url: '/api/account/019661f3-78c3-7a26-9ccf-361042fa4f67', params: [
            'email' => 'user@example.com',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
