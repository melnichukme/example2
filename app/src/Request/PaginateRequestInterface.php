<?php

namespace App\Request;

interface PaginateRequestInterface
{
    public function getPage(): int;
    public function getPerPage(): int;
}
