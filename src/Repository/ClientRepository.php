<?php

namespace App\Repository;

use App\Entity\Client;
use App\model\SearchData;
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
    public function findclientsWithEntrepriseDetails(int $page): PaginationInterface
    {
        $users = $this->createQueryBuilder('c')
            ->select('c.id, c.nom, c.prenom, c.email, c.telephone, e.nom as entreprise_nom, c.create_at,c.adresse')
            ->innerJoin('c.id_entreprise', 'e')
            ->addOrderBy('c.create_at', 'DESC')

            ->getQuery()
            ->getResult();



        return $this->paginator->paginate($users, $page, 5);
    }

    public function findBySearchData(SearchData $searchData): PaginationInterface
    {
        $users = $this->createQueryBuilder('c')
            ->select('c.id,c.nom, c.prenom, c.email, c.telephone, e.nom as entreprise_nom,c.create_at,c.adresse')
            ->innerJoin('c.id_entreprise', 'e')
            ->where('LOWER(c.nom) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.prenom) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.email) LIKE LOWER(:q)')
            ->orWhere('LOWER(e.nom) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.adresse) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $searchData->q . '%')
            ->addOrderBy('c.create_at', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->paginator->paginate($users, $searchData->page, 5);
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
