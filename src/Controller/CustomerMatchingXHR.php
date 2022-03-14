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

    const SKIP_DOMAINS = [
        "googlemail.com", "gmail.com", "yahoo.com", "yahoo.de", "aol.com", "aol.de",
        "live.com", "live.de", "hotmail.com", "hotmail.de", "outlook.com", "outlook.de",
        "msn.com", "msn.de", "gmx.de", "gmx.net", "web.de", "mail.ru", "freenet.de", "t-online.de",
        "mail.com", "mail.de", "icloud.com", "online.de"
    ];

    /**
     * @Route(
     *  "/%uvdesk_site_path.member_prefix%/wmt/skip-domains/list",
     *  name="webmatic_skip_domains_list"),
     *  methods={"HEAD", "GET"})
     */
    function getSkipDomains() : Response {
        return $this->json($this::SKIP_DOMAINS);
    }

    /**
     * @Route(
     *  "/%uvdesk_site_path.member_prefix%/wmt/customer/{customer}/matchcode",
     *  name="webmatic_get_kunde_matchcode"),
     *  methods={"HEAD", "GET"})
     */
    function getKundeMatchcode(User $customer, EntityManagerInterface $em) : Response {
        $match = $em->getRepository(UserKundeMatch::class)
                    ->findOneByUser($customer);
        $matchcode = "Ohne Kunde";
        if ($match) {
            $matchcode = $match->getKunde()->getMatchcode();
        }

        return new Response($matchcode);
    }

    /**
     * @Route(
     *  "/%uvdesk_site_path.member_prefix%/wmt/check/auto-assign",
     *  name="webmatic_check_auto_assign_domain"),
     *  methods={"HEAD", "GET"})
     */
    function checkAutoDomainAssignment(ORMEntityManagerInterface $em) : Response {
        
        $kunden = $em->getRepository(Kunde::class)
                    ->findAll();

        $domains = [];
        foreach ($kunden as $k) {
            if ($email = $k->getEmail()) {
                $domain = substr($email, strpos($email, "@") + 1);
                if (!in_array($domain, $this::SKIP_DOMAINS)) {
                    $domains[] = $domain;
                }
            }
        }

        $uniqueDomains = array_unique($domains);
        $multipleDomains = [];
        foreach ($uniqueDomains as $uniqueDomain) {
            $v = array_keys($domains, $uniqueDomain);
        
            if (count($v) > 1) {
                $multipleDomains[] = $uniqueDomain;        
            }
        }
        
        if (count($multipleDomains) > 0) {
            $domainResponse = implode(", ", $multipleDomains);
            return $this->json(["alertClass" => "warning", "alertMessage" => "Doppelte Domains: " . $domainResponse . ". Korrektur notwendig, um Zuordnung fehlerfrei durchführen zu können."], 400);
        } else {
            return $this->json([]);
        }
        

    }

    /**
     * @Route(
     *  "/{_locale}/%uvdesk_site_path.member_prefix%/wmt/customer/auto-assign",
     *  name="webmatic_customer_auto_assign_domain"),
     *  methods={"HEAD", "PUT"})
     */
    function autoDomainAssignment(ORMEntityManagerInterface $em) : Response {

        try {

            $kunden = $em->getRepository(Kunde::class)
                        ->findAll();

            $entities = [];
            foreach ($kunden as $k) {
                if ($email = $k->getEmail()) {
                    $domain = substr($email, strpos($email, "@") + 1);
                    if (!in_array(strtolower($domain), $this::SKIP_DOMAINS)) {
                        $entity = new KundeDomain();
                        if ($persisted = $em->getRepository(KundeDomain::class)
                                            ->findOneByKunde($k)
                        ) {
                            $entity = $persisted;
                        }
                        $entity->setDomain($domain)->setKunde($k);
                        $entities[] = $entity;
                    }
                }
            } 
            
            $em->saveEntities($entities);

            $msg = "Die Domains der Kontaktadressen wurden automatisch den Kunden zugewiesen.";
            $this->addFlash("success", $msg);
            return $this->json(["alertClass" => "success", "alertMessage" => $msg]);
        } catch( Exception $e) {
            $msg = "Beim Zuweisen der Domains ist ein Fehler aufgetreten: ".$e->getMessage();
            $this->addFlash("warning", $msg);
            return $this->json(["error" => $msg], 400);
        }    

    }

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
     *  "/{_locale}/%uvdesk_site_path.member_prefix%/wmt/customer/match/{customer}/{domain}/reset",
     *  name="webmatic_customer_reset_domain"),
     *  requirements={"customer":"\d+","domain":"^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$"},
     *  methods={"HEAD", "POST"})
     */
    function resetDomainMatch(ORMEntityManagerInterface $em, Kunde $customer, string $domain) : Response {

        try {

            $entity = $em->getRepository(KundeDomain::class)
                        ->findOneBy(["kunde" => $customer, "domain" => $domain]);
            $em->remove($entity);
            $em->flush();

            $msg = "Die Zurdnung $domain zu ".$customer->getMatchcode()." wurde entfernt.";
            return $this->json(["alertClass" => "success", "alertMessage" => $msg]);
        } catch( Exception $e) {
            $msg = "Beim Entfernen der Zuordnung ist ein Fehler aufgetreten: ".$e->getMessage();
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