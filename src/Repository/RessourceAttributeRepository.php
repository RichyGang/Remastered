<?php

namespace App\Repository;

use App\Entity\RessourceAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RessourceAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method RessourceAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method RessourceAttribute[]    findAll()
 * @method RessourceAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RessourceAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RessourceAttribute::class);
    }

    // /**
    //  * @return RessourceAttribute[] Returns an array of RessourceAttribute objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RessourceAttribute
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
