<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Localization\LocaleCode;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\Fixture\AccountActivatedEmmaFixture;
use Tests\Support\HttpClientRequestsTrait;

final class SignupNewAccountWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;

    #[Override]
    protected function setUp(): void
    {
        self::activateHttpClient();
    }

    public function testEnsureUserCanSignup(): void
    {
        self::loadFixtures([AccountActivatedEmmaFixture::class]);
        self::sendPostRequest(url: '/api/auth/signup', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        self::assertResponseContentSame(expectedContent: '');
        self::assertEmailCount(count: 1);
    }

    public function testTryToSignupWithInvalidCredentials(): void
    {
        self::sendPostRequest(url: '/api/auth/signup', params: [
            'email' => 'example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSignupWithNonexistentCredentials(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendPostRequest(url: '/api/auth/signup', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToSignupWithInvalidTypes(): void
    {
        self::sendPostRequest(url: '/api/auth/signup', params: [
            'email' => 'example.com',
            'password' => ['password4#account'],
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
