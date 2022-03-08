<?php

namespace App\Repository;

use App\Entity\UserKundeMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserKundeMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserKundeMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserKundeMatch[]    findAll()
 * @method UserKundeMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserKundeMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserKundeMatch::class);
    }

    // /**
    //  * @return UserKundeMatch[] Returns an array of UserKundeMatch objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserKundeMatch
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
