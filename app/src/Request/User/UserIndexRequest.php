<?php

namespace App\Request\User;

use App\Entity\User;
use App\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class UserIndexRequest extends BaseRequest
{
    protected array $availableFields = [
        'role',
        'full_name',
        'active'
    ];

    protected $full_name;

    protected $role;

    #[Assert\Choice([1, 0])]
    protected $active;
}
