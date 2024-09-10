<?php

namespace App\Controller\Api;

use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AbstractSymfonyController;

class AbstractController extends AbstractSymfonyController
{
    /**
     * @param Pagerfanta $pagerfanta
     * @return array
     */
    final protected function paginateResponse(Pagerfanta $pagerfanta): array
    {
        return [
            'items' => $pagerfanta->getCurrentPageResults(),
            'paginator' => [
                'current_page' => $pagerfanta->getCurrentPage(),
                'has_previous_page' => $pagerfanta->hasPreviousPage(),
                'has_next_page' => $pagerfanta->hasNextPage(),
                'per_page' => $pagerfanta->getMaxPerPage(),
                'total_items' => $pagerfanta->getNbResults(),
                'total_pages' => $pagerfanta->getNbPages(),
            ],
        ];
    }
}