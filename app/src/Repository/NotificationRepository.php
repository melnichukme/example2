<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\NotificationRead;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param User $user
     * @param array $ids
     * @param array $roles
     * @return array
     */
    final public function getNewNotifications(User $user, array $ids, array $roles): array
    {
        $builder = $this->createQueryBuilder('n')
            ->andWhere('n.createdAt > :createdAt and (n.user = :user or n.role IN(:roles) )')
            ->setParameters(
                [
                    'createdAt' => (new \DateTime("now"))->modify('-5 day'),
                    'user' => $user,
                    'roles' => $roles,
                ]
            )
            ->orderBy('n.id', 'DESC');

        if (!empty($ids)) {
            $builder->andWhere('n.id not in (:ids)')->setParameter('ids', $ids);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * @param array $ids
     * @param User $user
     * @return array
     */
    final public function getNotificationsByIds(array $ids, User $user): array
    {
        $builder = $this->createQueryBuilder('n')
            ->andWhere('(n.user = :user or n.role IN(:roles)) and n.id IN(:ids)')
            ->setParameters(
                [
                    'user' => $user,
                    'ids' => $ids,
                    'roles' => $user->getRoles(),
                ]
            );

        return $builder->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @return array
     */
    final public function getOldNotifications(User $user): array
    {
        $q = $this->createQueryBuilder('n')
            ->leftJoin(NotificationRead::class, 'nr', 'with', 'n.id = nr.notification')
            ->andWhere('n.createdAt > :createdAt and (n.user = :user or n.role IN(:roles))')
            ->andWhere('nr.notification is not null')
            ->andWhere('nr.user = :user')
            ->setParameters(array(
                'createdAt' => (new \DateTime("now"))->modify('-5 day'),
                'user' => $user,
                'roles' => $user->getRoles(),
            ))
            ->orderBy('n.id', 'DESC')
            ->getQuery();

        return $q->getResult();
    }

//    /**
//     * @return Notify[] Returns an array of Notify objects
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

//    public function findOneBySomeField($value): ?Notify
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
