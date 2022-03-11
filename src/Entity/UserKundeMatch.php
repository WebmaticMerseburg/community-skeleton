<?php

namespace App\Entity;

use App\Repository\UserKundeMatchRepository;
use Doctrine\ORM\Mapping as ORM;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\User;
use Webmatic\KundeBundle\Entity\MappedSuperclass\Kunde;

/**
 * @ORM\Entity(repositoryClass=UserKundeMatchRepository::class)
 * @ORM\Table(name="user_kunde_matching")
 */
class UserKundeMatch
{
    /**
     * @ORM\Id
     * @var \Webkul\UVDesk\CoreFrameworkBundle\Entity\User
     * 
     * @ORM\ManyToOne(targetEntity="Webkul\UVDesk\CoreFrameworkBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
    * @ORM\Id 
    * @var \Webmatic\KundeBundle\Entity\MappedSuperclass\Kunde
    *
    * @ORM\ManyToOne(targetEntity="Webmatic\KundeBundle\Entity\Kunde")
    * @ORM\JoinColumn(name="kunde_id", referencedColumnName="kunde_id", onDelete="CASCADE")
    */
    private $kunde;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getKunde(): Kunde
    {
        return $this->kunde;
    }

    public function setUser(User $user): self
    {   
        $this->user = $user;
        return $this;
    }

    public function setKunde(Kunde $kunde): self
    {
        $this->kunde = $kunde;
        return $this;
    }

}
