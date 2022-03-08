<?php

namespace App\Controller;

use App\Entity\KundeDomain;
use App\Entity\UserKundeMatch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\User;
use Webmatic\UtilBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Webmatic\KundeBundle\Entity\User as WmtUser;

class WMTCustomerXHR extends AbstractController {

    /**
     * @Route("/%uvdesk_site_path.member_prefix%/wmt/customer/list", name="webmatic_ticket_user_list")
     */
    function listCustomersXHR(EntityManagerInterface $em): Response {

        $qb = $em->createQueryBuilder();
        $qb->select('a,userInstance')
            ->from(User::class, 'a');
        $qb->leftJoin('a.userInstance', 'userInstance');

        $qb->andwhere('userInstance.supportRole = :roles');
        $qb->setParameter('roles', 4);

        $uv_users = $qb->getQuery()->getResult();

        $uk_matches = $em->getRepository(UserKundeMatch::class)->findAll();
        $matches = [];
        foreach ($uk_matches as $m) {
            $matches[$m->getUser()->getId()] = $m->getKunde()->getMatchcode();
        }

        $json = [];

        foreach ($uv_users as $user) {
            $mail = $user->getEmail();
            $text = $user->getFullName()." ($mail)";
            $text .= (array_key_exists($user->getId(), $matches) ? " - " . $matches[$user->getId()] : "");

            $json[$mail] = $text;

        }

        return new Response(json_encode($json));

    }

    /**
     * @Route("/{_locale}/%uvdesk_site_path.member_prefix%/wmt/customer/{domain}/domain", name="webmatic_customer_by_domain")
     *  requirements={"domain":"^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$"},
     */
    function getKundeByDomainXHR(EntityManagerInterface $em, string $domain): Response {

        $match = $em->getRepository(KundeDomain::class)
                    ->findOneByDomain($domain);

        $json = [];
        $code = 200;

        if ($match) {
            $json = ["matchcode" => $match->getKunde()->getMatchcode()];
        } else {
            $json = ["alertClass" => "danger", "alertMessage" => "Es konnte kein Kunde zur Domain $domain gefunden werden."];
            $code = 400;
        }

        return new Response(json_encode($json), $code);

    }

}