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
    public function getProducts(int $page):PaginationInterface
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createNativeQuery(
            'SELECT * FROM product p INNER JOIN category c ON p.category_id_id = c.id',
            new \Doctrine\ORM\Query\ResultSetMapping()
        );

        $results = $query->getResult();
        return $this->paginator->paginate($results, $page, 2);

    }

    public function getAllProducts(int $page):PaginationInterface
    {
        // Créer une requête pour récupérer tous les produits
        $products = $this->createQueryBuilder('p')
            ->addOrderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->paginator->paginate($products, $page, 15);



    }


    public function findByProductNameOrCategoryName(SearchData $searchData,int $page):PaginationInterface
    {
        $products = $this->createQueryBuilder('p')
            ->select('p, c')
            ->innerJoin('p.category', 'c')
            ->where('p.name LIKE (:q) OR c.name LIKE (:q)')
            ->setParameter('q', '%' . $searchData->q . '%')
            ->getQuery()
            ->getResult();

        return $this->paginator->paginate($products, $page, 5);
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
