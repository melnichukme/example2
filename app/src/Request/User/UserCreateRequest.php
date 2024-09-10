<?php

namespace App\Request\User;

use App\Entity\User;
use App\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class UserCreateRequest extends BaseRequest
{
    protected array $availableFields = [
        'username',
        'role',
        'password',
        'first_name',
        'last_name',
        'third_name'
    ];

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank()]
    protected $username;

    #[Assert\Choice([User::ROLE_ADMIN])]
    #[Assert\NotBlank()]
    protected $role;

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 6, max: 16)]
    protected $password;

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank()]
    protected $first_name;

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank()]
    protected $last_name;

    #[Assert\Type(type: 'string')]
    protected $third_name;
}