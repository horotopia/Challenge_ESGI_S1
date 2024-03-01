<?php

namespace App\Repository;

use App\Entity\Client;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Client::class);
    }
    /**
     * @param int $page
     * @return PaginationInterface
     */
    public function findclientsWithEntrepriseDetails(int $page, $companyId, $userRole): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('c.id, c.lastName, c.firstName, c.email, c.phone, e.name as companyName, c.createdAt,c.address')
            ->innerJoin('c.companyId', 'e');
        if (!in_array('ROLE_ADMIN', $userRole)) {
            $queryBuilder->where('e.id = :companyId')
                ->setParameter('companyId', $companyId);
        }


        $queryBuilder->addOrderBy('c.createdAt', 'DESC');

        $clients = $queryBuilder->getQuery()->getResult();

        return $this->paginator->paginate($clients, $page, 5);
    }

    public function findBySearchData(SearchData $searchData, $companyId, $userRole): PaginationInterface
    {


        $queryBuilder = $this->createQueryBuilder('c')
            ->select('c.id, c.lastName, c.firstName, c.email, c.phone, e.name as companyName, c.createdAt, c.address')
            ->innerJoin('c.companyId', 'e')
            ->where('LOWER(c.lastName) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.firstName) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.email) LIKE LOWER(:q)')
            ->orWhere('LOWER(e.name) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.address) LIKE LOWER(:q)');

        if (!in_array('ROLE_ADMIN', $userRole)) {
            $queryBuilder->andWhere('e.id = :companyId')
                    ->setParameter('companyId', $companyId);
        }


        $queryBuilder->setParameter('q', '%' . $searchData->q . '%')
            ->addOrderBy('c.createdAt', 'DESC');

        $users = $queryBuilder->getQuery()->getResult();

        return $this->paginator->paginate($users, $searchData->page, 5);
    }

    public function countClientsAddedToday($companyId): int
    {
        $currentDate = new \DateTime();

        $startDate = new \DateTime($currentDate->format('Y-m-d'));

        $endDate = new \DateTime($currentDate->format('Y-m-d 23:59:59'));

        // Ajouter une seconde à la date de fin de journée pour inclure toute la journée
        $endDate->modify('+1 second');

        $qb = $this->createQueryBuilder('c');
        $qb->select($qb->expr()->count('c.id'));
        $qb->where('c.createdAt BETWEEN :startDate AND :endDate');
        $qb->andWhere('c.companyId = :companyId');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('companyId', $companyId);

        return (int)$qb->getQuery()->getSingleScalarResult();
    }




    /**
     * Récupère les clients appartenant à une entreprise spécifique.
     *
     * @param int $companyId Identifiant de l'entreprise.
     * @return Client[] Liste des clients de l'entreprise.
     */
    public function findByCompany(int $companyId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.companyId = :companyId')
            ->setParameter('companyId', $companyId)
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Client
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
