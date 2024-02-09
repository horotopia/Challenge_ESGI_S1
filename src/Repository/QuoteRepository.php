<?php

namespace App\Repository;

use App\Entity\Quote;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Quote>
 *
 * @method Quote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quote[]    findAll()
 * @method Quote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Quote::class);
    }
    /**
     * @param int $page
     * @return PaginationInterface
     */
    public function findQuoteDetails(int $page): PaginationInterface
    {
        $quote = $this->createQueryBuilder('d')
            ->select('d.id,d.quotationNumber, d.createdAt, d.status, c.lastName, c.firstName, e.name as companyName, d.totalHT, d.totalTTC')
            ->innerJoin('d.clientId', 'c')
            ->innerJoin('c.companyId', 'e')
            ->addOrderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();



        return $this->paginator->paginate($quote, $page, 5);
    }

    public function findBySearchData(SearchData $searchData): PaginationInterface
    {

        $quote = $this->createQueryBuilder('d')
            ->select('d.id,d.quotationNumber,d.createdAt, d.status, c.lastName, c.firstName, e.name as companyName, d.totalHT, d.totalTTC')
            ->innerJoin('d.clientId', 'c')
            ->innerJoin('c.companyId', 'e')
            ->where('LOWER(d.quotationNumber) LIKE LOWER(:q)')
            ->orWhere('LOWER(d.status) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.lastName) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.firstName) LIKE LOWER(:q)')
            ->orWhere('LOWER(e.name) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $searchData->q . '%')
            ->addOrderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
        return $this->paginator->paginate($quote, $searchData->page, 5);
    }

    public function countPendingQuotesForCurrentMonth(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(q.id) as quoteCount, SUM(q.totalTTC) as totalAmount
            FROM App\Entity\Quote q
            WHERE q.status = :status
            AND q.createdAt >= :startOfMonth
            AND q.createdAt <= :endOfMonth'
        )->setParameter('status', 'Envoyé')
            ->setParameter('startOfMonth', new \DateTime('first day of this month'))
            ->setParameter('endOfMonth', new \DateTime('last day of this month'));

        return $query->getResult();
    }
    public function countExpiredQuotesForCurrentMonth(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(q.id) as quoteCount, SUM(q.totalTTC) as totalAmount
        FROM App\Entity\Quote q
        WHERE q.dueDate <= :currentDate'
        )->setParameter('currentDate', new \DateTime());

        return $query->getResult();
    }


}
