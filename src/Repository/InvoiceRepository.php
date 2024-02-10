<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * Retrieves detailed information about invoices and paginates the results.
     *
     * @param int $page The current page number.
     * @return PaginationInterface The paginated list of invoices.
     */
    public function findInvoiceDetails(int $page): PaginationInterface
    {
        $query = $this->createQueryBuilder('i')
            ->select('i.id, i.invoiceNumber, i.createdAt, i.status, i.amount, c.lastName, c.firstName')
            ->leftJoin('i.client', 'c')
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery();

        return $this->paginator->paginate($query, $page, 5);
    }

    public function findBySearchData(SearchData $searchData): PaginationInterface
    {
        $query = $this->createQueryBuilder('i')
            ->select('i.invoiceNumber, i.createdAt, i.status, i.amount, c.lastName, c.firstName')
            ->leftJoin('i.client', 'c')
            ->leftJoin('i.quote', 'q')
            ->where('LOWER(i.invoiceNumber) LIKE LOWER(:q)')
            ->orWhere('LOWER(i.status) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.lastName) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.firstName) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $searchData->q . '%')
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery();

        return $this->paginator->paginate($query, $searchData->page, 5);
    }
}