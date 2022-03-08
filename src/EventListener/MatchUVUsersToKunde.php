<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\KundeDomain;
use App\Entity\UserKundeMatch;
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

final class MatchUVUsersToKunde implements EventSubscriber
{
    /**
    * @var EntityManagerInterface
    */
    private EntityManagerInterface $em;

    public function __construct() { }

    /**
    * {@inheritDoc}
    */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $this->em = $event->getEntityManager();

        if ($entity instanceof KundeDomain) {
            $users = $this->em->createQueryBuilder()
                              ->select("u")
                              ->from(UVDeskUser::class, "u")
                              ->where("u.email LIKE :domain")
                              ->setParameter("domain", "%".$entity->getDomain()."%")
                              ->getQuery()->getResult();

            $mappings = [];
            foreach ($users as $u) {
                $existingMapping = $this->em->getRepository(UserKundeMatch::class)
                                ->findOneBy(["user" => $u, "kunde" => $entity->getKunde()]);
                if (!$existingMapping) {
                    $mappings[] = (new UserKundeMatch())
                                    ->setUser($u)
                                    ->setKunde($entity->getKunde());
                }
                                
            }
            $this->em->saveEntities($mappings);
        }

        if ($entity instanceof UVDeskUser) {
            $domain = substr(
                        $entity->getEmail(),
                        strpos($entity->getEmail(), "@")+1
                    );
            $kunde = $this->em->getRepository(KundeDomain::class)
                                ->findOneByDomain($domain);
            
            if ($kunde) {
                $match = new UserKundeMatch();
                $match->setKunde($kunde)->setUser($entity);
                $this->em->saveEntity($match);
            }
        }
        
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $this->em = $event->getEntityManager();
        if ($entity instanceof KundeDomain) {
            $users = $this->em->createQueryBuilder()
                              ->select("u")
                              ->from(UVDeskUser::class, "u")
                              ->where("u.email LIKE :domain")
                              ->setParameter("domain", "%".$entity->getDomain()."%")
                              ->getQuery()->getResult();
            
            $oldMatching = $this->em->getRepository(UserKundeMatch::class)
                                    ->findByKunde($entity->getKunde());
            
            foreach ($oldMatching as $old) {
                $this->em->remove($old);
            }
            $this->em->flush();

            $mappings = [];
            foreach ($users as $u) {
                $existingMapping = $this->em->getRepository(UserKundeMatch::class)
                                ->findOneBy(["user" => $u, "kunde" => $entity->getKunde()]);
                if (!$existingMapping) {
                    $mappings[] = (new UserKundeMatch())
                                    ->setUser($u)
                                    ->setKunde($entity->getKunde());
                }
            }
            $this->em->saveEntities($mappings);
        }

        if ($entity instanceof UVDeskUser) {
            $domain = substr(
                        $entity->getEmail(),
                        strpos($entity->getEmail(), "@")+1
                    );
            $newKunde = $this->em->getRepository(KundeDomain::class)
                                ->findOneByDomain($domain)
                                ->getKunde();
            $oldMatch = $this->em->getRepository(UserKundeMatch::class)
                                ->findOneByUser($entity);
            
            if ($oldMatch) {
                $oldKunde = $oldMatch->getKunde();
                if ($oldKunde !== $newKunde && !empty($newKunde)) {
                    $oldMatch->setKunde($newKunde);
                    $this->em->saveEntity($oldMatch);
                }
            } else {
                if ($newKunde) {
                    $match = new UserKundeMatch();
                    $match->setKunde($newKunde)->setUser($entity);
                    $this->em->saveEntity($match);
                }
            }
        }
        
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $this->em = $event->getEntityManager();
        if ($entity instanceof KundeDomain) {
            $oldMatching = $this->em->getRepository(UserKundeMatch::class)
                                    ->findByKunde($entity->getKunde());
            
            foreach ($oldMatching as $old) {
                $this->em->remove($old);
            }
            $this->em->flush();
        }

        if ($entity instanceof UVDeskUser) {
            
        }
        
    }

}
