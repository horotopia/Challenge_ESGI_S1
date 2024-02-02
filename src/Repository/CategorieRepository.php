<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Categorie::class);
    }

//    /**
//     * @return Categorie[] Returns an array of Categorie objects
//     */
//    public function findCategorieCountProduits($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
   
//    public function getCategoriesWithProductCount()
//     {
//         $sql = '
//             SELECT c.*, COUNT(p.id) as productCount
//             FROM categorie c
//             LEFT JOIN produit p ON c.id = p.id_categorie_id
//             GROUP BY c.id
//         ';

//         $query = $this->getEntityManager()->createNativeQuery($sql, new \Doctrine\ORM\Query\ResultSetMapping());

//         return $query->getResult();
//     }


     /**
     * @param int $page
     * @return PaginationInterface
     */
public function getCategoriesWithProductCount(int $page): PaginationInterface
    {
        $cat= $this->createQueryBuilder('c')
            ->select('c', 'COUNT(p.id) as productCount')
            ->leftJoin('c.produits', 'p') // Assurez-vous que le champ 'produits' correspond à la relation dans votre entité Categorie
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
            
            return $this->paginator->paginate($cat, $page, 8);
    }



//    public function findOneBySomeField($value): ?Categorie
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
