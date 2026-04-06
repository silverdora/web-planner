<?php

namespace App\Services;

use App\Models\User;

interface IUserService
{
    /**
     * Get a user by its id.
     *
     * @param int $id
     *
     * @return User|null
     */
    public function getById(int $id): ?User;

    /**
     * Get user data by email address.
     *
     * @param string $email
     *
     * @return array|null Associative array or null if not found.
     */
    public function getByEmail(string $email): ?array;

    /**
     * Create a new user from the provided data.
     *
     * @param array $user
     *
     * @return User
     */
    public function create(array $user): User;

    /**
     * Update an existing user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function update(User $user): bool;

    /**
     * Delete a user by id.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;
}
