<?php

namespace App\Service;

use App\Entity\Country;
use App\Entity\Region;
use App\Repository\CountryRepository;
use App\Repository\RegionRepository;

class RegionService
{
    /**
     * RegionService constructor.
     * @param RegionRepository $regionRepository
     */
    public function __construct(protected RegionRepository $regionRepository)
    {
    }

    /**
     * @param array|null $criteria
     * @return array
     */
    public function getList(array $criteria = null): array
    {
        return $this->regionRepository->findAll();
    }

    /**
     * @param string $title
     * @return Region
     */
    public function create(string $title): Region
    {
        $entity = new Region();
        $entity->setTitle($title);

        $this->regionRepository->save($entity, true);

        return $entity;
    }

    /**
     * @param int $id
     * @param string $title
     * @return Region
     */
    final public function update(int $id, string $title): Region
    {
        $entity = $this->getById($id);
        $entity->setTitle($title);

        $this->regionRepository->save($entity, true);

        return $entity;
    }

    /**
     * @param int $id
     * @return Region
     */
    final public function delete(int $id): Region
    {
        $entity = $this->getById($id);

        $this->regionRepository->remove($entity, true);

        return $entity;
    }

    /**
     * @param string $title
     * @return Region|null
     */
    public function getByTitle(string $title):? Region
    {
        return $this->regionRepository->findOneBy(['title' => $title]);
    }

    /**
     * @param int $id
     * @return Region|null
     */
    final public function getById(int $id):? Region
    {
        return $this->regionRepository->find($id);
    }
}
