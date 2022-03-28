<?php

declare(strict_types=1);

namespace App\Security;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Webkul\UVDesk\CoreFrameworkBundle\Providers\UserProvider as UVDeskUserProvider;

final class UserProvider implements UserProviderInterface
{
    /**
    * @var Connection
    */
    private $connection;

    /**
    * @var UVDeskUserProvider
    */
    private $uvDeskUserProvider;


    public function __construct(
        Connection $connection,
        UVDeskUserProvider $uvDeskUserProvider
    )
    {
        $this->connection = $connection;
        $this->uvDeskUserProvider = $uvDeskUserProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        try {
            return $this->uvDeskUserProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            $email = $this->connection
                ->createQueryBuilder()
                ->select('email')
                ->from('user')
                ->where("username = :username")
                ->setMaxResults(1)
                ->setParameter('username', $username)
                ->execute()
                ->fetchOne()
            ;
            if (false === $email) {
                throw $e;
            }
            return $this->uvDeskUserProvider->loadUserByUsername($email);
        }

    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->uvDeskUserProvider->refreshUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $this->uvDeskUserProvider->supportsClass($class);
    }
}
