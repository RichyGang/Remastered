<?php

namespace App\Repository;

use App\Entity\ProposalAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProposalAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProposalAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProposalAttribute[]    findAll()
 * @method ProposalAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProposalAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProposalAttribute::class);
    }

    // /**
    //  * @return ProposalAttribute[] Returns an array of ProposalAttribute objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProposalAttribute
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
