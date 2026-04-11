<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Localization\LocaleCode;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedJamesFixture;
use Tests\Support\HttpClientRequestsTrait;

final class CreateNewAccountWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::activateHttpClient();
    }

    public function testEnsureAdminCanCreateAccount(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendPostRequest(url: '/api/account', params: [
            'email' => 'created@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_CREATED);
        self::assertResponseSchema(schema: 'CreateNewAccountSchema.json');
    }

    public function testTryToCreateAccountWithExistingEmail(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        self::sendPostRequest(url: '/api/account', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToCreateAccountWithoutPermission(): void
    {
        self::loadFixtures([AccountActivatedJamesFixture::class]);
        self::sendAuthorizationRequest(email: 'james@example.com', password: 'password4#account');
        self::sendPostRequest(url: '/api/account', params: [
            'email' => 'created@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
