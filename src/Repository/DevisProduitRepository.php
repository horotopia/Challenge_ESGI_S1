<?php

namespace App\Repository;

use App\Entity\DevisProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DevisProduit>
 *
 * @method DevisProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevisProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevisProduit[]    findAll()
 * @method DevisProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisProduit::class);
    }

//    /**
//     * @return DevisProduit[] Returns an array of DevisProduit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DevisProduit
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
