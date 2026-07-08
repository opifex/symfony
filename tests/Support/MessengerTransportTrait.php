<?php

declare(strict_types=1);

namespace Tests\Support;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

trait MessengerTransportTrait
{
    public static function getMessengerTransport(string $name): TransportInterface
    {
        $transport = self::getContainer()->get(id: 'messenger.transport.' . $name);
        self::assertInstanceOf(expected: TransportInterface::class, actual: $transport);

        return $transport;
    }

    public static function countMessengerTransportMessages(string $name): int
    {
        $transport = self::getMessengerTransport($name);
        self::assertInstanceOf(expected: MessageCountAwareInterface::class, actual: $transport);

        return $transport->getMessageCount();
    }

    public static function purgeMessengerTransport(string $name): void
    {
        $transport = self::getMessengerTransport($name);

        while (self::countMessengerTransportMessages($name) > 0) {
            foreach ($transport->get() as $envelope) {
                $transport->reject($envelope);
            }
        }
    }

    public static function consumeMessengerTransport(string $name): void
    {
        $transport = self::getMessengerTransport($name);
        $messageBus = self::getContainer()->get(id: 'messenger.routable_message_bus');
        self::assertInstanceOf(expected: MessageBusInterface::class, actual: $messageBus);

        while (self::countMessengerTransportMessages($name) > 0) {
            foreach ($transport->get() as $envelope) {
                $messageBus->dispatch($envelope->with(new ReceivedStamp($name)));
                $transport->ack($envelope);
            }
        }
    }
}
