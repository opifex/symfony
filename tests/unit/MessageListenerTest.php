<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\MailerMessageListener;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageListenerTest extends Unit
{
    private MailerMessageListener $messageListener;

    /**
     * @throws MockObjectException
     */
    protected function setUp(): void
    {
        $translator = $this->createMock(originalClassName: TranslatorInterface::class);
        $this->messageListener = new MailerMessageListener($translator);
    }

    /**
     * @throws MockObjectException
     */
    public function testInvokeWithNotificationEmail(): void
    {
        $rawMessage = $this->createMock(originalClassName: NotificationEmail::class);
        $envelope = $this->createMock(originalClassName: Envelope::class);

        ($this->messageListener)(new MessageEvent($rawMessage, envelope: $envelope, transport: 'email'));
    }
}
