<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Listener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsDoctrineListener(event: Events::prePersist)]
final class PrePersistListener
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function __invoke(PrePersistEventArgs $event): void
    {
        $constraints = $this->validator->validate($event->getObject());

        if ($constraints->count()) {
            throw new ValidationFailedException($event->getObject(), $constraints);
        }
    }
}
