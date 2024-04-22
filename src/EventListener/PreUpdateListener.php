<?php

namespace App\EventListener;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::preUpdate, connection: 'default')]
final class PreUpdateListener
{
    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();
        $dateTimeNow = new DateTimeImmutable('now');
        $entity->setUpdatedAt($dateTimeNow);
    }
}
