<?php

namespace App\Repository;

use App\Entity\NotificationRead;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationRead>
 *
 * @method NotificationRead|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationRead|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationRead[]    findAll()
 * @method NotificationRead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationReadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationRead::class);
    }

    public function save(NotificationRead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NotificationRead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllByInIds(array $ids, User $user)
    {
        $q = $this->createQueryBuilder('nr')
            ->where('nr.notification IN(:ids) and nr.user = :user')
            ->setParameters(array(
                'ids' => $ids,
                'user' => $user
            ))
            ->getQuery();

        return $q->getResult();
    }

//    /**
//     * @return NotificationRead[] Returns an array of NotificationRead objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NotificationRead
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
