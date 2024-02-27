<?php

namespace App\Repository;

use App\Entity\Users;
use App\Entity\Company;
use App\Model\SearchData;
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
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
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
    public function findUsersWithCompanyDetails(int $page): PaginationInterface
    {
        $users = $this->createQueryBuilder('u')
            ->select('u.id, u.lastName, u.firstName, u.email, u.roles, c.name as companyName, u.createdAt')
            ->innerJoin('u.companyId', 'c')
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
            ->select('u.id,u.lastName, u.firstName, u.email, u.roles, c.name as companyName, u.createdAt')
            ->innerJoin('u.companyId', 'c')
            ->where('LOWER(u.lastName) LIKE LOWER(:q)')
            ->orWhere('LOWER(u.firstName) LIKE LOWER(:q)')
            ->orWhere('LOWER(u.email) LIKE LOWER(:q)')
            ->orWhere('LOWER(c.name) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $searchData->q . '%')
            ->addOrderBy('u.createdAt', 'DESC')
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
