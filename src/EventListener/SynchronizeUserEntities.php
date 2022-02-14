<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\SupportRole;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\User as UVDeskUser;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\UserInstance;
use Webkul\UVDesk\CoreFrameworkBundle\Services\UserService;
use Webmatic\DoctrineBundle\ORM\EntityManagerInterface;
use Webmatic\UserBundle\Entity\MappedSuperclass\User as PortalUser;
use Webmatic\UserBundle\Entity\User as PortalUserEntity;

final class SynchronizeUserEntities implements EventSubscriber
{
    /**
    * @var EntityManagerInterface
    */
    private $entityManager;

    /**
    * @var UserService
    */
    private $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
    * {@inheritDoc}
    */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $this->entityManager = $event->getEntityManager();
        if ($entity instanceof PortalUser) {
            $this->savePortalUser($entity);
        } elseif ($entity instanceof UVDeskUser) {
            $this->saveUVDeskUser($entity);
        }
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $entity = $event->getEntity();
        $this->entityManager = $event->getEntityManager();
        if ($entity instanceof UVDeskUser) {
            if ($event->hasChangedField('email')) {
                $event->setNewValue('email', $event->getOldValue());
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $this->entityManager = $event->getEntityManager();
        if ($entity instanceof PortalUser) {
            $this->savePortalUser($entity);
        } elseif ($entity instanceof UVDeskUser) {
            $this->saveUVDeskUser($entity);
        } elseif ($entity instanceof UserInstance) {
            $this->saveUVDeskUser($entity->getUser());
        }
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $this->entityManager = $event->getEntityManager();
        if ($entity instanceof PortalUser) {
            $uvDeskUser = $this->entityManager->getRepository(UVDeskUser::class)
                ->findOneByEmail($entity->getEmail())
            ;
            if (!empty($uvDeskUser)) {
                $this->entityManager->remove($uvDeskUser);
                $this->entityManager->flush();
            }
        } elseif ($entity instanceof UVDeskUser) {
            $this->saveUVDeskUser($entity);
        } elseif ($entity instanceof UserInstance) {
            $this->saveUVDeskUser($entity->getUser());
        }
    }

    private function removeUserInstance(UVDeskUser $user, UserInstance $userInstance): void
    {
        $user->removeUserInstance($userInstance);
        $this->entityManager->remove($userInstance);
        $this->entityManager->saveEntity($user);
    }

    private function savePortalUser(PortalUser $portalUser): void
    {
        $isAgent = false;
        $isCustomer = false;
        $roleName = null;
        if (in_array('ROLE_CUSTOMER', $portalUser->getRoles(), true)) {
            $isCustomer = true;
            $roleName = 'ROLE_CUSTOMER';
        }
        $agentRole = null;
        foreach ([ 'ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_AGENT' ] as $role) {
            if (in_array($role, $portalUser->getRoles(), true)) {
                $isAgent = true;
                $roleName = $role;
                break;
            }
        }

        $supportRole = $this->entityManager->getRepository(SupportRole::class)
            ->findOneByCode($roleName)
        ;

        $uvDeskUser = $this->userService->createUserInstance(
            $portalUser->getEmail(),
            $portalUser->getUsername(),
            $supportRole,
            [
                'active' => $portalUser->isEnabled(),
                'source' => 'portal'
            ]
        );

        $agentInstance = $uvDeskUser->getAgentInstance();
        $customerInstance = $uvDeskUser->getCustomerInstance();

        if ($isAgent) {
            if (null === $agentInstance) {
                $this->userService->createUserInstance(
                    $portalUser->getEmail(),
                    $portalUser->getUsername(),
                    $supportRole,
                    [
                        'active' => $portalUser->isEnabled(),
                        'source' => 'portal'
                    ]
                );
            } else {
                $agentInstance->setSupportRole($supportRole);
                $this->entityManager->saveEntity($agentInstance);
            }
        }

        if ($isCustomer && null === $customerInstance) {
            $supportRole = $this->entityManager->getRepository(SupportRole::class)
                ->findOneByCode('ROLE_CUSTOMER')
            ;
            $this->userService->createUserInstance(
                $portalUser->getEmail(),
                $portalUser->getUsername(),
                $supportRole,
                [
                    'active' => $portalUser->isEnabled(),
                    'source' => 'portal'
                ]
            );
        }

        if (!$isAgent && null !== $agentInstance) {
            $this->removeUserInstance($uvDeskUser, $agentInstance);
        }
        if (!$isCustomer && null !== $customerInstance) {
            $this->removeUserInstance($uvDeskUser, $customerInstance);
        }
    }

    private function saveUVDeskUser(UVDeskUser $uvDeskUser): void
    {
        $portalUser = $this->entityManager->getRepository(PortalUserEntity::class)
            ->findOneByEmail($uvDeskUser->getEmail())
        ;
        if (empty($portalUser)) {
            $this->entityManager->remove($uvDeskUser);
            $this->entityManager->flush();
        } else {
            $this->savePortalUser($portalUser);
        }
    }
}
