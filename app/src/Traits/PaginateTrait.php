<?php

namespace App\Traits;

use App\Request\PaginateRequestInterface;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

trait PaginateTrait
{
    /**
     * @param QueryBuilder $qb
     * @param PaginateRequestInterface $paginateRequest
     * @return Pagerfanta
     */
    final public function getPaginated(QueryBuilder $qb, PaginateRequestInterface $paginateRequest): Pagerfanta
    {
        $paginator = new Pagerfanta(new QueryAdapter($qb));
        $paginator->setMaxPerPage($paginateRequest->getPerPage());
        $paginator->setCurrentPage($paginateRequest->getPage());

        return $paginator;
    }
}