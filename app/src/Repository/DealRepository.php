<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\ContractContragent;
use App\Entity\Contragent;
use App\Entity\Deal;
use App\Enums\ContractStatusEnum;
use App\Request\Deal\DealIndexDto;
use App\Traits\PaginateTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;

/**
 * @extends ServiceEntityRepository<Deal>
 *
 * @method Deal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deal[]    findAll()
 * @method Deal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealRepository extends ServiceEntityRepository
{
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deal::class);
    }

    /**
     * @param DealIndexDto $dto
     * @return Pagerfanta
     */
    final public function getAll(DealIndexDto $dto): Pagerfanta
    {
        $builder = $this->createQueryBuilder('d')->orderBy('d.id', 'DESC');

        if ($dto->getQueryFilter()) {
            $builder->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->like('d.number', "concat('%', :like_number, '%')")
                )
            )
                ->setParameter('like_number', trim($dto->getQueryFilter()));
        }

        if ($dto->getType()) {
            $builder->innerJoin(Contract::class, 'c', 'with', 'd.id = c.deal')
                ->andWhere('c.type = :type')
                ->setParameter('type', $dto->getType());
        }

        if ($dto->getStatus()) {
            $builder->andWhere('d.status = :status')
                ->setParameter('status', ContractStatusEnum::getStatusIdBySlug($dto->getStatus()));
        }

        if ($dto->getLawyerId()) {
            $builder->andWhere('d.assignedBy = :lawyer')
                ->setParameter('lawyer', $dto->getLawyerId());
        }

        if ($dto->getContragent()) {
            $builder->innerJoin(Contract::class, 'c', 'with', 'd.id = c.deal')
                ->innerJoin(ContractContragent::class, 'cc', 'with', 'cc.contract = c.id')
                ->where('cc.contragent = :contragent')
                ->setParameter('contragent', $dto->getContragent());
        }

        return $this->getPaginated($builder, $dto);
    }
}
