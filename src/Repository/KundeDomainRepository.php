<?php

namespace App\Repository;

use App\Entity\KundeDomain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method KundeDomain|null find($id, $lockMode = null, $lockVersion = null)
 * @method KundeDomain|null findOneBy(array $criteria, array $orderBy = null)
 * @method KundeDomain[]    findAll()
 * @method KundeDomain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KundeDomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KundeDomain::class);
    }

    // /**
    //  * @return KundeDomain[] Returns an array of KundeDomain objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?KundeDomain
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
