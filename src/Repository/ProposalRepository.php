<?php

namespace App\Repository;

use App\Entity\Proposal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Proposal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proposal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proposal[]    findAll()
 * @method Proposal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProposalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proposal::class);
    }


    /**
     * @return Proposal[] Returns an array of Proposal objects except of user's proposal
     */
    public function findAllExcept($user)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.author != :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Proposal[] Returns an array of Proposal objects except of user's proposal
//     */
//    public function findAllCombo($user, $category)
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.author != :user')
//            ->andWhere('p.ressource = :cat')
//            ->setParameter('user', $user)
//            ->setParameter('cat', $category)
//            ->getQuery()
//            ->getResult();
//    }

    // /**
    //  * @return Proposal[] Returns an array of Proposal objects
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
    public function findOneBySomeField($value): ?Proposal
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
