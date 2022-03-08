<?php

namespace App\Controller;

use App\Entity\KundeDomain;
use App\Entity\UserKundeMatch;
use App\Repository\KundeDomainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\User;
use Webmatic\UtilBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Webmatic\DoctrineBundle\ORM\EntityManagerInterface as ORMEntityManagerInterface;
use Webmatic\KundeBundle\Entity\Kunde;

class CustomerMatchingXHR extends AbstractController {

    /**
     * @Route(
     *  "/{_locale}/%uvdesk_site_path.member_prefix%/wmt/customer/match/{customer}/{domain}/set",
     *  name="webmatic_customer_match_domain"),
     *  requirements={"customer":"\d+","domain":"^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$"},
     *  methods={"HEAD", "POST"})
     */
    function setDomainMatch(ORMEntityManagerInterface $em, Kunde $customer, string $domain) : Response {

        try {

            // insert Kunde-Domain mapping in KundeDomain
            // auto-mapping related users via EventListener
            $entity = new KundeDomain();
            if ($persisted = $em->getRepository(KundeDomain::class)
                                ->findOneByKunde($customer)
            ) {
                $entity = $persisted;
            }

            $entity->setKunde($customer)->setDomain($domain);
            $em->saveEntity($entity);

            $msg = "Die Domain $domain wurde ".$customer->getMatchcode()." zugewiesen.";
            return $this->json(["alertClass" => "success", "alertMessage" => $msg]);
        } catch( Exception $e) {
            $msg = "Beim Zuweisen der Domain $domain ist ein Fehler aufgetreten: ".$e->getMessage();
            return $this->json(["error" => $msg], 400);
        }    

    }

    /**
     * @Route(
     *  "/{_locale}/%uvdesk_site_path.member_prefix%/wmt/user/match/{user}/{customer}/set",
     *  name="webmatic_customer_match_user"),
     *  requirements={"user":"\d+|(__USER__)","customer":"\d+|(__CUSTOMER__)"},
     *  methods={"HEAD", "POST"})
     */
    function setUserKundeMatch(ORMEntityManagerInterface $em, Kunde $customer, User $user) : Response {

        try {

            $entity = new UserKundeMatch();
            if ($persisted = $em->getRepository(UserKundeMatch::class)
                                ->findOneByUser($user)
            ) {
                $entity = $persisted;
            }

            $entity->setKunde($customer)->setUser($user);
            $em->saveEntity($entity);

            $msg = "Der Kunde ".$customer->getMatchcode()." wurde Nutzer ".$user->getFullName()." zugeordnet.";
            return $this->json(["alertClass" => "success", "alertMessage" => $msg]);
        } catch( Exception $e) {
            $msg = "Beim Zuweisen des Kunden ". $customer->getMatchcode() ." ist ein Fehler aufgetreten: ".$e->getMessage();
            return $this->json(["error" => $msg], 400);
        }    

    }

}