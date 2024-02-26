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
            ->select('i,c')
            ->leftJoin('i.client', 'c')
            ->orderBy('i.createdAt', 'DESC');

        if (!in_array('ROLE_ADMIN', $userRole)) {
            $queryBuilder->where('c.companyId = :companyId')
                ->setParameter('companyId', $companyId);
        }

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate($query, $page, 10);
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

        return $this->paginator->paginate($query, $searchData->page, 10);
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
            ->setParameter('status', 'Payé')
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
    public function countSalesAddedToday($companyId, $status): int
    {
        $currentDate = new \DateTime();

        $startDate = new \DateTime($currentDate->format('Y-m-d'));

        $endDate = new \DateTime($currentDate->format('Y-m-d 23:59:59'));

        $qb = $this->createQueryBuilder('i');
        $qb->select($qb->expr()->count('i.id'));
        $qb->where('i.createdAt BETWEEN :startDate AND :endDate');
        $qb->andWhere('i.client IN (SELECT c.id FROM App\Entity\Client c WHERE c.companyId = :companyId)');
        $qb->andWhere('i.status = :status');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('companyId', $companyId);
        $qb->setParameter('status', $status);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    public function countTotalSales($companyId, $status): int
    {
       $qb = $this->createQueryBuilder('i');
        $qb->select($qb->expr()->count('i.id'));
        $qb->Where('i.client IN (SELECT c.id FROM App\Entity\Client c WHERE c.companyId = :companyId)');
        $qb->andWhere('i.status = :status');
        $qb->setParameter('companyId', $companyId);
        $qb->setParameter('status', $status);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    public function getInvoiceTypesByMonth($companyId): array
    {
        $currentDate = new \DateTime();

        $query = $this->_em->createQueryBuilder()
            ->select('i.createdAt AS createdAt, i.status AS status, i.dueDate AS dueDate')
            ->from('App\Entity\Invoice', 'i')
            ->leftJoin('i.client', 'c')
            ->where('c.companyId = :companyId')
            ->setParameter('companyId', $companyId)
            ->getQuery();

        $invoices = $query->getResult();

        $invoiceTypesByMonth = [];

        foreach ($invoices as $invoice) {
            $createdAt = $invoice['createdAt'];
            $dueDate = $invoice['dueDate'];

            if ($createdAt instanceof \DateTime && $dueDate instanceof \DateTimeInterface) {
                $month = $createdAt->format('n');
                $status = $invoice['status'];

                if ($currentDate > $dueDate && $status === 'En attente') {
                    $status = 'En retard';
                }

                if (!isset($invoiceTypesByMonth[$month][$status])) {
                    $invoiceTypesByMonth[$month][$status] = 0;
                }

                $invoiceTypesByMonth[$month][$status]++;
            }
        }

        return $invoiceTypesByMonth;
    }
    public function getRevenueByMonth($companyId): array
    {
        $invoices = $this->createQueryBuilder('i')
            ->select('i.totalTTC, i.createdAt')
            ->leftJoin('i.client', 'c')
            ->where('c.companyId = :companyId')
            ->andWhere("i.status = :status")
            ->setParameter('companyId', $companyId)
            ->setParameter("status", "Payé")
            ->getQuery()
            ->getResult();

        $revenueByMonth = [];

        foreach ($invoices as $invoice) {
            $createdAt = $invoice['createdAt'];
            $totalTTC = $invoice['totalTTC'];
            $month = $createdAt->format('n');

            if (!isset($revenueByMonth[$month])) {
                $revenueByMonth[$month] = 0;
            }

            $revenueByMonth[$month] += $totalTTC;
        }
        return $revenueByMonth;
    }


    public function calculateRevenueOnFiscalYear($companyId): float
    {
        $startDate = new \DateTimeImmutable('first day of January this year');
        $endDate = new \DateTimeImmutable('last day of December this year');

        $qb = $this->createQueryBuilder('i');
        $qb->select('SUM(i.totalTTC)');
        $qb->where('i.createdAt BETWEEN :startDate AND :endDate');
        $qb->andWhere('i.client IN (SELECT c.id FROM App\Entity\Client c WHERE c.companyId = :companyId)');
        $qb->andWhere('i.status=:status');
        $qb->setParameter('status', 'Payé');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('companyId', $companyId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }
    public function calculateRevenueOfPreviousFiscalYear($companyId): float
    {

        $startDate = new \DateTimeImmutable('first day of January last year');
        $endDate = new \DateTimeImmutable('last day of December last year');

        $qb = $this->createQueryBuilder('i');
        $qb->select('SUM(i.totalTTC)');
        $qb->where('i.createdAt BETWEEN :startDate AND :endDate');
        $qb->andWhere('i.client IN (SELECT c.id FROM App\Entity\Client c WHERE c.companyId = :companyId)');
        $qb->andWhere('i.status=:status');
        $qb->setParameter('status', 'Payé');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('companyId', $companyId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }


    public function calculateRevenueByMonth($companyId): float
    {
        $startDate = new \DateTime( 'first day of this month');
        $endDate = (clone $startDate)->modify('last day of this month');
        $qb = $this->createQueryBuilder('i');
        $qb->select('SUM(i.totalTTC)');
        $qb->where('i.createdAt BETWEEN :startDate AND :endDate');
        $qb->andWhere('i.client IN (SELECT c.id FROM App\Entity\Client c WHERE c.companyId = :companyId)');
        $qb->andWhere('i.status=:status');
        $qb->setParameter('status', 'Payé');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('companyId', $companyId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }
    public function calculateRevenuePreviousMonth($companyId): float
    {
        $startDate = new \DateTime('first day of last month');
        $endDate = (clone $startDate)->modify('last day of this month');

        $qb = $this->createQueryBuilder('i');
        $qb->select('SUM(i.totalTTC)');
        $qb->where('i.createdAt BETWEEN :startDate AND :endDate');
        $qb->andWhere('i.client IN (SELECT c.id FROM App\Entity\Client c WHERE c.companyId = :companyId)');
        $qb->andWhere('i.status=:status');
        $qb->setParameter('status', 'Payé');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('companyId', $companyId);

        $revenuePreviousMonth = (float) $qb->getQuery()->getSingleScalarResult();
        return $revenuePreviousMonth;
    }


    public function calculateRevenueByDay($companyId): float
    {
        $startDate = new \DateTime();
        $endDate = (clone $startDate)->modify('+1 day');
        $qb = $this->createQueryBuilder('i');
        $qb->select('SUM(i.totalTTC)');
        $qb->where('i.createdAt BETWEEN :startDate AND :endDate');
        $qb->andWhere('i.client IN (SELECT c.id FROM App\Entity\Client c WHERE c.companyId = :companyId)');
        $qb->andWhere('i.status=:status');
        $qb->setParameter('status', 'Payé');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('companyId', $companyId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }


    public function getAllPayments($page,$companyId): PaginationInterface
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT i, c
        FROM App\Entity\Invoice i
        JOIN i.client c
        WHERE i.status = :status and c.companyId= :company'
        )->setParameter('status', 'Payé')
            ->setParameter('company', $companyId);

        return $this->paginator->paginate($query, $page, 10);

    }

    public function getAllPaymentsBySearch(SearchData $searchData,$page,$userRole,$companyId): PaginationInterface
    {

        $query = $this->createQueryBuilder('i')
            ->select('i,c')
            ->innerJoin('i.client ', 'c')
            ->where('c.companyId = :company ')
            ->andwhere('LOWER(c.lastName) LIKE LOWER(:search) ')
            ->orWhere('LOWER(c.firstName) LIKE LOWER(:search) ')
            ->andWhere('LOWER(i.status) LIKE LOWER(:status) ')

            ->setParameter('status', 'Payé')
            ->setParameter('company', $companyId)
            ->setParameter('search', $searchData->q);




        return $this->paginator->paginate($query, $page, 10);

    }


}