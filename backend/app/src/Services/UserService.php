<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\IUserRepository;
use App\Repositories\UserRepository;

class UserService implements IUserService
{
    private IUserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    /**
     * Get a user by id.
     *
     * @param int $id
     *
     * @return User|null
     */
    public function getById(int $id): ?User
    {
        return $this->repository->getById($id);
    }

    /**
     * Get a user row by email.
     *
     * @param string $email
     *
     * @return array|null
     */
    public function getByEmail(string $email): ?array
    {
        return $this->repository->getByEmail($email);
    }

    /**
     * Create a new user.
     *
     * @param array $user
     *
     * @return User
     */
    public function create(array $user): User
    {
        return $this->repository->create($user);
    }

    /**
     * Update a user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function update(User $user): bool
    {
        return $this->repository->update($user);
    }

    /**
     * Delete a user by id.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
