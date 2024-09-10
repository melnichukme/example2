<?php

namespace App\Repository;

use App\Entity\Artist;
use App\Entity\Deal;
use App\Entity\User;
use App\Request\Artist\ArtistIndexDto;
use App\Request\Contract\ContractIndexDto;
use App\Traits\PaginateTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;

/**
 * @extends ServiceEntityRepository<Artist>
 *
 * @method Artist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository
{
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    /**
     * @param ArtistIndexDto $dto
     * @return Pagerfanta
     */
    final public function getAll(ArtistIndexDto $dto): Pagerfanta
    {
        $builder = $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC');

        if ($dto->getQueryFilter()) {
            $builder->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->like('lower(a.name)', "concat('%', :like_name, '%')")
                )
            )
                ->setParameter('like_name', mb_strtolower($dto->getQueryFilter()));
        }

        if ($dto->getCreatedAtTo()) {
            $builder->andWhere('a.createdAt BETWEEN :created_at_from AND :created_at_to')
                ->setParameter('created_at_from', $dto->getCreatedAtFrom()->setTime(0, 0, 0))
                ->setParameter('created_at_to', $dto->getCreatedAtTo()->setTime(23, 59, 59));
        }

        if ($dto->getUpdatedAtTo()) {
            $builder->andWhere('a.updatedAt BETWEEN :updated_at_from AND :updated_at_to')
                ->setParameter('updated_at_from', $dto->getUpdatedAtFrom()->setTime(0, 0, 0))
                ->setParameter('updated_at_to', $dto->getUpdatedAtTo()->setTime(23, 59, 59));
        }

        return $this->getPaginated($builder, $dto);
    }

    /**
     * @param string $uuid
     * @return Artist|null
     */
    public function findByUuid(string $uuid): ?Artist
    {
        return $this->findOneBy([
            'uuid' => $uuid
        ]);
    }

    /**
     * @param string|null $string
     * @return array
     */
    public function findByLikeName(?string $string): array
    {
        $qb = $this->createQueryBuilder('a');

        if ($string) {
            $qb->andWhere($qb->expr()->like('a.name', ':name'))
                ->setParameter('name', '%' . addcslashes($string, '%_') . '%');
        }

        $qb->setMaxResults(30);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $ulids
     * @return array
     */
    public function getByUlids(array $ulids): array
    {
        $qb = $this->createQueryBuilder('a');

        $results = [];
        if (count($ulids)) {
            $results = $qb->andWhere(
                $qb->expr()->in('a.ulid', $ulids)
            )
                ->getQuery()
                ->getResult();
        }

        return $results;
    }

    /**
     * @param int $limit
     * @param int|null $offsetLastId
     * @return array
     */
    public function getUlidsLimitOffsetById(int $limit, int $offsetLastId = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id', 'a.ulid')
            ->where("a.ulid <> :empty")
            ->setParameter('empty', '')
            ->setMaxResults($limit)
            ->orderBy('a.id', 'ASC');

        if (!is_null($offsetLastId)) {
            $qb->where('a.id > :offset_last_id')
                ->setParameter('offset_last_id', $offsetLastId);
        }

        $qb = $qb->getQuery()
            ->useQueryCache(false);

        $ulids = [];
        foreach ($qb->toIterable() as $row) {
            $ulids[$row['id']] = $row['ulid'];
        }

        return $ulids;
    }
}
