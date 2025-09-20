<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\Fixture\AccountActivatedAdminFixture;
use Tests\Support\HttpClientComponentTrait;

final class GetSigninAccountWebTest extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientComponentTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testEnsureUserCanGetSigninAccountWithValidBearer(): void
    {
        $this->loadFixtures([AccountActivatedAdminFixture::class]);
        $this->sendAuthorizationRequest(email: 'admin@example.com', password: 'password4#account');
        $this->sendGetRequest(url: '/api/auth/me');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        $this->assertResponseSchema(schema: 'GetSigninAccountSchema.json');
    }

    public function testTryToGetSigninAccountWithoutAuthorizationHeader(): void
    {
        $this->sendGetRequest(url: '/api/auth/me');
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToGetSigninAccountWithInvalidAuthorizationHeader(): void
    {
        $this->sendGetRequest(url: '/api/auth/me', server: ['HTTP_Authorization' => 'invalid']);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }

    public function testTryToGetSigninAccountWithInvalidBearerToken(): void
    {
        $this->sendGetRequest(url: '/api/auth/me', server: ['HTTP_Authorization' => 'Bearer invalid']);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
        $this->assertResponseSchema(schema: 'ApplicationExceptionSchema.json');
    }
}
