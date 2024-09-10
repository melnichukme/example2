<?php

namespace App\Repository;

use App\Entity\Credential;
use App\Entity\CredentialDetail;
use App\Enums\CredentialDetailStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CredentialDetail>
 *
 * @method CredentialDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method CredentialDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method CredentialDetail[]    findAll()
 * @method CredentialDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CredentialDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CredentialDetail::class);
    }

    /**
     * @param string|null $string
     * @return array
     */
    public function findByLikeName(?string $string): array
    {
        $qb = $this->createQueryBuilder('cd');

        if ($string) {
            $string = mb_strtolower($string);

            $qb->andWhere($qb->expr()->like('lower(cd.value)', "concat('%', :string, '%')"))
                ->setParameter('string', '%' . addcslashes($string, '%_') . '%');
        }

        $qb->setMaxResults(30);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $ids
     * @return CredentialDetail[]
     */
    public function getByCredentialIds(array $ids): array
    {
        $qb = $this->createQueryBuilder('cd');
        $qb->andWhere("cd.credential IN(:ids)")
            ->setParameter('ids', array_values($ids));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Credential $credential
     * @param string $fieldName
     * @param string $value
     * @return CredentialDetail
     */
    final public function create(
        Credential $credential,
        string $fieldName,
        string $value,
    ): CredentialDetail
    {
        $entity = new CredentialDetail();
        $entity->setCredential($credential);
        $entity->setFieldName($fieldName);
        $entity->setValue($value);
        $entity->setStatus(CredentialDetailStatusEnum::DEFAULT->value);

        $this->getEntityManager()->persist($entity);

        return $entity;
    }
}
