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

class CustomerMatching extends AbstractController {

    /**
     * @Route("/{_locale}/%uvdesk_site_path.member_prefix%/wmt/customer/preset/{page<\d+>?1}", name="webmatic_customer_matching_preset")
     */
    function matchCustomersPreset(EntityManagerInterface $em, int $page): Response {

        $maxResults = 30;
        $wmt_customer_count = count($em->getRepository(Kunde::class)->findAll());
        $pages = max(1, ceil($wmt_customer_count / $maxResults));
        
        $wmt_customer_query = $em->createQueryBuilder()
                            ->select("k")
                            ->from(Kunde::class, "k")
                            ->orderBy("k.matchcode")
                            ->setMaxResults($maxResults)
                            ->setFirstResult($maxResults * ($page - 1));

        $wmt_customers = new Paginator($wmt_customer_query);
        
        $kunde_domains = $em->createQueryBuilder()
                            ->select("kd")
                            ->from(KundeDomain::class, "kd")
                            ->getQuery()->getResult();
        
        $user_domains_result = $em->createQueryBuilder()
                            ->select("SUBSTRING(u.email, LOCATE('@',u.email) + 1) as domain")
                            ->distinct()
                            ->from(User::class, "u")
                            ->leftJoin('u.userInstance', 'i')
                            ->andwhere('i.supportRole = :roles')
                            ->setParameter('roles', 4)
                            ->orderBy("u.email")
                            ->getQuery()->getScalarResult();

        $user_domains = [];
        foreach ($user_domains_result as $ud) {
            $user_domains[] = $ud["domain"];
        }

        $domain_matches = [];
        foreach ($kunde_domains as $kd) {
            $domain_matches[$kd->getKunde()->getId()] = $kd->getDomain();
            $user_domains[] = $kd->getDomain();
        }
        
        $user_domains = array_unique($user_domains);

        return $this->render("CustomerMatching/matchWmtCustomersPreset.html.twig", [
            "customers" => $wmt_customers,
            "domains"   => $user_domains,
            "domainMatches" => $domain_matches,
            "page" => $page,
            "pages" => $pages,
            "maxResults" => $maxResults,
            "resultCount" => $result_count,
            "customerCount" => $wmt_customer_count
        ]);

    }

    /**
     * @Route("/{_locale}/%uvdesk_site_path.member_prefix%/wmt/customer/matching", name="webmatic_customer_matching")
     */
    function matchCustomers(EntityManagerInterface $em): Response {

        $uv_users = $em->createQueryBuilder()
                            ->select("u")
                            ->from(User::class, "u")
                            ->orderBy("u.lastName")
                            ->setMaxResults(20)
                            ->getQuery()->getResult();

        $kunden = $em->createQueryBuilder()
                    ->select("k")
                    ->from(Kunde::class, "k")
                    ->orderBy("k.matchcode")
                    ->getQuery()->getResult();

        $uk_matches = $em->getRepository(UserKundeMatch::class)->findAll();
        $matches = [];
        foreach ($uk_matches as $m) {
            $matches[$m->getUser()->getId()] = $m->getKunde()->getId();
        }
 
        return $this->render("CustomerMatching/matchWmtCustomers.html.twig", [
            "users" => $uv_users,
            "kunden" => $kunden,
            "matches" => $matches
        ]);

    }

}