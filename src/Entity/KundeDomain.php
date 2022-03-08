<?php

namespace App\Entity;

use App\Repository\KundeDomainRepository;
use Doctrine\ORM\Mapping as ORM;
use Webmatic\KundeBundle\Entity\Kunde;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=KundeDomainRepository::class)
 * @UniqueEntity(fields={"kunde", "domain"})
 */
class KundeDomain
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Webmatic\KundeBundle\Entity\Kunde")
    * @ORM\JoinColumn(name="kunde_id", referencedColumnName="kunde_id", onDelete="CASCADE")
    */
    private $kunde;

    /**
     * @ORM\Column(type="string", length=63)
     */
    private $domain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKunde(): ?Kunde
    {
        return $this->kunde;
    }

    public function setKunde(?Kunde $kunde): self
    {
        $this->kunde = $kunde;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }
}
