<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Contract\CommandMessageBusInterface;
use App\Application\Contract\QueryMessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;

abstract class AbstractController extends BaseController
{
    public function __construct(
        protected readonly CommandMessageBusInterface $commandMessageBus,
        protected readonly QueryMessageBusInterface $queryMessageBus,
    ) {
    }
}
