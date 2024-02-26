<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function getProducts(int $page,$companyId,$userRole): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.description, p.brand, p.unitPrice, p.VAT, p.availableQuantity, p.createdAt, c.name as categoryName, c.id as categoryId')
            ->innerJoin('p.categoryId', 'c');
           if (!in_array('ROLE_ADMIN', $userRole)) {
            $queryBuilder->where('p.companyId = :companyId')
                ->setParameter('companyId', $companyId);
        }

        $results = $queryBuilder->getQuery()->getResult();
        return $this->paginator->paginate($results, $page, 2);
    }

    public function getAllProducts(int $page):PaginationInterface
    {
        // Créer une requête pour récupérer tous les produits
        $products = $this->createQueryBuilder('p')
            ->addOrderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->paginator->paginate($products, $page, 2);



    }


    public function findByProductNameOrCategoryName(SearchData $searchData,int $page):PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.description, p.brand, p.unitPrice, p.VAT, p.availableQuantity, p.createdAt, c.name as categoryName, c.id as categoryId')
            ->innerJoin('p.categoryId', 'c')
            ->where('LOWER(p.name) LIKE :q ')
            ->setParameter('q', '%' . $searchData->q . '%');

         $products=$queryBuilder->getQuery()->getResult();

        return $this->paginator->paginate($products, $page, 5);

    }
    public function countProductsAddedToday($companyId): int
    {
        $currentDate = new \DateTime();

        $startDate = new \DateTime($currentDate->format('Y-m-d'));

        $endDate = new \DateTime($currentDate->format('Y-m-d 23:59:59'));
        $endDate->modify('+1 second');

        $qb = $this->createQueryBuilder('p');
        $qb->select($qb->expr()->count('p.id'));
        $qb->where('p.createdAt BETWEEN :startDate AND :endDate');
        $qb->andWhere('p.companyId = :companyId');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('companyId', $companyId);

        return (int)$qb->getQuery()->getSingleScalarResult();

}

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
