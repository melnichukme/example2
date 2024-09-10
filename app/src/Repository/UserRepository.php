<?php

namespace App\Repository;

use App\Entity\User;
use App\Filter\UserFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $id
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getById(int $id):? User
    {
        $qb = $this->createQueryBuilder('u');

        $qb->andWhere('u.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param UserFilter $filter
     * @return array
     */
    public function getAllWithFilter(UserFilter $filter): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($fullName = $filter->getFullName()) {
            $qb->andWhere("concat(coalesce(u.firstName, ''), ' ', coalesce(u.lastName, ''), ' ', coalesce(u.thirdName, '')) like concat('%', :full_name, '%')")
                ->setParameter('full_name', $fullName);
        }

        if ($roles = $filter->getRole()) {
            $orX = $qb->expr()->orX();

            foreach ($roles as $key => $role) {
                $parameterName = 'role' . $key;
                $orX->add('CONTAINS(TO_JSONB(u.roles), :' . $parameterName . ') = TRUE');
                $qb->setParameter($parameterName, '["'.$role.'"]');
            }
            $qb->andWhere($orX);
        }

        if ($filter->isActive()) {
            $qb->andWhere('u.isActive = :active')
                ->setParameter('active', $filter->isActive());
        }

        return $qb->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
