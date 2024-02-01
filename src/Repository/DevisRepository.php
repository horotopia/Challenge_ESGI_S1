<?php

namespace App\Repository;

use App\Entity\Devis;
use App\model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Devis>
 *
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Devis::class);
    }
    /**
     * @param int $page
     * @return PaginationInterface
     */
    public function findDevisDetails(int $page): PaginationInterface
    {
        $devis = $this->createQueryBuilder('d')
            ->select('d.id,d.num_devis, d.create_at, d.statut, c.nom, c.prenom, e.nom as entrepriseNom, d.totalHT, d.totalTTC')
            ->innerJoin('d.id_client', 'c')
            ->innerJoin('c.id_entreprise', 'e')
            ->addOrderBy('d.create_at', 'DESC')
            ->getQuery()
            ->getResult();



        return $this->paginator->paginate($devis, $page, 5);
    }

    public function findBySearchData(SearchData $searchData): PaginationInterface
    {

        $devis = $this->createQueryBuilder('d')
            ->select('d.id,d.num_devis,d.create_at, d.statut, c.nom, c.prenom, e.nom as entrepriseNom, d.totalHT, d.totalTTC')
            ->innerJoin('d.id_client', 'c')
            ->innerJoin('c.id_entreprise', 'e')
            ->where('LOWER(d.num_devis) LIKE LOWER(:q)')
            ->orWhere('LOWER(d.statut) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.nom) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.prenom) LIKE LOWER(:q)')
            ->orWhere('LOWER(e.nom) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $searchData->q . '%')
            ->addOrderBy('c.create_at', 'DESC')
            ->getQuery()
            ->getResult();
        return $this->paginator->paginate($devis, $searchData->page, 5);
    }


}
