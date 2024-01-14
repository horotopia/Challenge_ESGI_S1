<?php

namespace App\Repository;

use App\Entity\Users;
use App\Entity\Entreprise;
use App\model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Users>
 *
 * @implements PasswordUpgraderInterface<Users>
 *
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {

        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $page
     * @return PaginationInterface
     */
    public function findUsersWithEntrepriseDetails(int $page): PaginationInterface
    {
        $users = $this->createQueryBuilder('u')
            ->select('u.id, u.nom, u.prenom, u.email, u.roles, e.nom as entreprise_nom, u.created_at')
            ->innerJoin('u.id_entreprise', 'e')
            ->getQuery()
            ->getResult();


        $filteredUsers = array_filter($users, function ($user) {
            return !in_array('ROLE_ADMIN', $user['roles']);
            
        });

        
        return $this->paginator->paginate($filteredUsers, $page, 5);
        
    }


    public function findBySearchData(SearchData $searchData): PaginationInterface
    {
        $users = $this->createQueryBuilder('u')
            ->select('u.id,u.nom, u.prenom, u.email, u.roles, e.nom as entreprise_nom, u.created_at')
            ->innerJoin('u.id_entreprise', 'e')
            ->where('LOWER(u.nom) LIKE LOWER(:q)')
            ->orWhere('LOWER(u.prenom) LIKE LOWER(:q)')
            ->orWhere('LOWER(u.email) LIKE LOWER(:q)')
            ->orWhere('LOWER(e.nom) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $searchData->q . '%')
            ->addOrderBy('u.created_at', 'DESC')
            ->getQuery()
            ->getResult();
        $filteredUsers = array_filter($users, function ($user) {
            return !in_array('ROLE_ADMIN', $user['roles']);
        });
        return $this->paginator->paginate($filteredUsers, $searchData->page, 5);
    }



//    public function findOneBySomeField($value): ?Users
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
