<?php

namespace App\Dto\User;

use App\Request\User\UserCreateRequest;
use DateTime;
use Exception;

class UserCreateDto
{
    /**
     * @param string $username
     * @param string $role
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @param string|null $thirdName
     * @param DateTime $birthDate
     * @param int $office
     * @param int $rate
     * @param DateTime $startWorkData
     * @param string $socialLink
     * @param int|null $salesPlan
     */
    public function __construct(
        public readonly string $username,
        public readonly string $role,
        public readonly string $password,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $thirdName,
        public readonly DateTime $birthDate,
        public readonly int $office,
        public readonly int $rate,
        public readonly DateTime $startWorkData,
        public readonly string $socialLink,
        public readonly ?int $salesPlan,
    ) {
    }

    /**
     * @param UserCreateRequest $request
     * @return self
     * @throws Exception
     */
    public static function createFromRequest(UserCreateRequest $request): self
    {
        $data = $request->toArray();

        $socialLinks = empty($socialLinks) ? (object)[] : $socialLinks;

        return new self(
            $data['username'],
            $data['role'],
            $data['password'],
            $data['first_name'],
            $data['last_name'],
            $data['third_name'] ?? null,
            new DateTime($data['birth_date']),
            $data['office'],
            $data['rate'],
            new DateTime($data['start_work_date']),
            json_encode($socialLinks, JSON_THROW_ON_ERROR),
            $data['sales_plan'] ?? null,
        );
    }
}
