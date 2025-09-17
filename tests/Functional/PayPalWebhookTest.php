<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\HttpClientComponentTrait;

final class PayPalWebhookTest extends WebTestCase
{
    use HttpClientComponentTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->activateHttpClient();
    }

    public function testPaypalWebhookReceivesSuccessfully(): void
    {
        $webhookToken = $_ENV['PAYPAL_WEBHOOK_TOKEN'];
        $this->sendPostRequest(url: '/webhook/paypal?token=' . $webhookToken, params: [
            'id' => '8PT597110X687430LKGECATA',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_ACCEPTED);
    }

    public function testTryToSendWebhookWithoutToken(): void
    {
        $this->sendPostRequest(url: '/webhook/paypal', params: [
            'id' => '8PT597110X687430LKGECATA',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
    }

    public function testTryToSendWebhookWithInvalidPayload(): void
    {
        $webhookToken = $_ENV['PAYPAL_WEBHOOK_TOKEN'];
        $this->sendPostRequest(url: '/webhook/paypal?token=' . $webhookToken, params: [
            'id' => '8PT597110X687430LKGECATA',
        ]);
        $this->assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_ACCEPTABLE);
    }
}
