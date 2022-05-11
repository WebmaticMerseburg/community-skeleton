<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket;
use Webmatic\DoctrineBundle\ORM\EntityManagerInterface;

final class DisableTicketReply implements EventSubscriber
{
    /**
    * @var EntityManagerInterface
    */
    private $entityManager;

    /**
    * {@inheritDoc}
    */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate
        ];
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $this->entityManager = $event->getEntityManager();
        if ($entity instanceof Ticket) {
            if ($entity->getStatus()->getId() === 5) {
                $entity->setIsReplyEnabled(0);
                $this->entityManager->flush();
            }
        }
    }

}
