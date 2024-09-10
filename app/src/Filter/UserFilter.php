<?php

namespace App\Filter;

use App\Request\User\UserIndexRequest;

class UserFilter
{
    /**
     * @var string|null
     */
    private ?string $fullName = null;

    /**
     * @var array
     */
    private array $role = [];

    /**
     * @var bool
     */
    private bool $active;

    /**
     * @param UserIndexRequest $request
     */
    public function __construct(UserIndexRequest $request)
    {
        $data = $request->toArray();

        if (array_key_exists('role', $data)) {
            $roles = is_array($data['role']) ? $data['role'] : [$data['role']];
        } else {
            $roles = [];
        }

        $this->fullName = $data['full_name'] ?? null;
        $this->role = $roles;
        $this->active = (bool)($data['active'] ?? false);
    }

    /**
     * @return array
     */
    public function getRole(): array
    {
        return $this->role;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
