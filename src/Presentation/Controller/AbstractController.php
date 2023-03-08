<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class AbstractController
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected MessageBusInterface $commandBus,
        protected MessageBusInterface $queryBus,
        protected TokenStorageInterface $tokenStorage,
        protected UrlGeneratorInterface $urlGenerator,
    ) {
    }
}
