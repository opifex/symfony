<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Foundation\Enum\LocaleCode;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\HttpClientRequestsTrait;
use Tests\Support\MessengerTransportTrait;

final class SignupNewAccountWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestsTrait;
    use MessengerTransportTrait;

    #[Override]
    protected function setUp(): void
    {
        self::loadHttpClient();
    }

    public function testSignupWithValidEmailSendsConfirmation(): void
    {
        self::purgeMessengerTransport(name: 'domain_events');
        self::sendPostRequest(url: '/api/v1/auth/signup', params: [
            'email' => 'user@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
        self::consumeMessengerTransport(name: 'domain_events');
        self::assertEmailCount(count: 1);
        self::assertEmailAddressContains(self::getMailerMessage(), headerName: 'To', expectedValue: 'user@example.com');
        self::assertEmailSubjectContains(self::getMailerMessage(), expectedValue: 'Thank you for registration');
    }

    public function testSignupWithInvalidEmailFormatReturnsUnprocessableEntity(): void
    {
        self::sendPostRequest(url: '/api/v1/auth/signup', params: [
            'email' => 'example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertErrorResponseSchema();
    }

    public function testSignupWithAlreadyRegisteredEmailReturnsConflict(): void
    {
        self::loadFixtures([AccountActivatedAdminFixture::class]);
        self::sendPostRequest(url: '/api/v1/auth/signup', params: [
            'email' => 'admin@example.com',
            'password' => 'password4#account',
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_CONFLICT);
        self::assertErrorResponseSchema();
    }

    public function testSignupWithInvalidFieldTypesReturnsUnprocessableEntity(): void
    {
        self::sendPostRequest(url: '/api/v1/auth/signup', params: [
            'email' => 'example.com',
            'password' => ['password4#account'],
            'locale' => LocaleCode::EnUs->toString(),
        ]);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertErrorResponseSchema();
    }
}
