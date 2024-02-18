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
    public function findInvoiceDetails(int $page, $userRole,$companyId): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select('i.id, i.invoiceNumber, i.createdAt, i.status, i.totalTTC,i.totalHT, c.lastName, c.firstName')
            ->leftJoin('i.client', 'c')
            ->orderBy('i.createdAt', 'DESC');

        if (!in_array('ROLE_ADMIN', $userRole)) {
            $queryBuilder->where('c.companyId = :companyId')
                ->setParameter('companyId', $companyId);
        }

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate($query, $page, 5);
    }

    public function findBySearchData(SearchData $searchData,  $userRole,$companyId): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select('i.invoiceNumber, i.createdAt, i.status, i.totalTTC,i.totalHT, c.lastName, c.firstName')
            ->leftJoin('i.client', 'c')
            ->leftJoin('i.quote', 'q')
            ->where('LOWER(i.invoiceNumber) LIKE LOWER(:q)')
            ->orWhere('LOWER(i.status) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.lastName) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.firstName) LIKE LOWER(:q)');

        if (!in_array('ROLE_ADMIN', $userRole)) {
            $queryBuilder->andWhere('c.companyId = :companyId')
                ->setParameter('companyId', $companyId);
        }

        $query = $queryBuilder->setParameter('q', '%' . $searchData->q . '%')
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery();

        return $this->paginator->paginate($query, $searchData->page, 5);
    }



    public function getPaidInvoicesSummary($companyId): array
    {
        $query = $this->_em->createQueryBuilder()
            ->select('COUNT(i.id) AS count, SUM(i.totalTTC) AS total')
            ->from('App\Entity\Invoice', 'i')
            ->leftJoin('i.client', 'c')
            ->where('c.companyId = :companyId')
            ->andWhere('i.status = :status')
            ->setParameter('companyId', $companyId)
            ->setParameter('status', 'Reglée')
            ->getQuery();

        return $query->getResult();
    }


    public function getUnpaidInvoicesSummary($companyId): array
    {
        $query = $this->_em->createQueryBuilder()
            ->select('COUNT(i.id) AS count, SUM(i.totalTTC) AS total')
            ->from('App\Entity\Invoice', 'i')
            ->leftJoin('i.client', 'c')
            ->where('c.companyId = :companyId')
            ->andWhere('i.status = :status')
            ->setParameter('companyId', $companyId)
            ->setParameter('status', 'En attente')
            ->getQuery();

        return $query->getResult();
    }


    public function getOverdueInvoicesSummary($companyId): array
    {
        $currentDate = new \DateTime();
        $currentDate->setTime(0, 0, 0);

        $query = $this->_em->createQueryBuilder()
            ->select('COUNT(i.id) AS count, SUM(i.totalTTC) AS total')
            ->from('App\Entity\Invoice', 'i')
            ->innerJoin('i.client', 'c')
            ->where('c.companyId = :companyId')
            ->andWhere('i.dueDate <=:currentDate')
            ->setParameter('companyId', $companyId)
            ->setParameter('currentDate', $currentDate)
            ->getQuery();

        return $query->getResult();

}

}