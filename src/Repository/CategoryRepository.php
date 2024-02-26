<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Category::class);
    }

//    /**
//     * @return Category[] Returns an array of Category objects
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
//             FROM categories c
//             LEFT JOIN products p ON c.id = p.id_categorie_id
//             GROUP BY c.id
//         ';

//         $query = $this->getEntityManager()->createNativeQuery($sql, new \Doctrine\ORM\Query\ResultSetMapping());

//         return $query->getResult();
//     }


     /**
     * @param int $page
     * @return PaginationInterface
     */
public function getCategoriesWithProductCount(int $page,$companyId,$userRole): PaginationInterface
    {
        $category= $this->createQueryBuilder('c')
            ->select('c', 'COUNT(p.id) as productCount')
            ->leftJoin('c.products', 'p') // Assurez-vous que le champ 'products' correspond à la relation dans votre entité Category
            ->groupBy('c.id')
            ->where('c.company_id = :companyId')
            ->setParameter('companyId', $companyId)
            ->getQuery()
            ->getResult();
            
            return $this->paginator->paginate($category, $page, 10);
    }

    public function getCategoriesBySearch($searchData,int $page,$companyId,$userRole): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('c,c.name, COUNT(p.id) as productCount')
            ->leftJoin('c.products', 'p')
            ->groupBy('c.id')
            ->Where('LOWER(c.name) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $searchData->q . '%');
        if (!in_array('ROLE_ADMIN', $userRole)) {
            $queryBuilder->andWhere('com.id = :companyId')
                ->setParameter('companyId', $companyId);
        }

        $products=$queryBuilder->getQuery()->getResult();

        return $this->paginator->paginate($products, $page, 10);
    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
