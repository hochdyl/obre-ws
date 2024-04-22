<?php

namespace App\EventListener;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist, connection: 'default')]
final class PrePersistListener
{
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();
        $dateTimeNow = new DateTimeImmutable('now');
        $entity->setCreatedAt($dateTimeNow);
        $entity->setUpdatedAt($dateTimeNow);
    }
}
