<?php

namespace App\Request;

use App\Enums\LocaleEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileRequest extends BaseRequest
{
    protected array $availableFields = [
        'password',
        'first_name',
        'last_name',
        'third_name',
        'username',
        'locale'
    ];

    #[Assert\Type(type: 'string')]
    protected $password;

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank()]
    protected $first_name;

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank()]
    protected $username;

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank()]
    protected $last_name;

    #[Assert\Type(type: 'string')]
    protected $third_name;

    #[Assert\Choice([LocaleEnum::RU->value, LocaleEnum::EN->value])]
    protected $locale;
}