<?php

namespace App\Services;

use App\Models\Category;

interface ICategoryService
{
    /**
     * Get a category by id for a specific user.
     *
     * @param int $id
     * @param int $userId
     *
     * @return Category|null
     */
    public function getByIdAndUserId(int $id, int $userId): ?Category;

    /**
     * Create a new category for a user.
     *
     * @param int   $userId
     * @param array $data
     *
     * @return Category
     */
    public function create(int $userId, array $data): Category;

    /**
     * Update a category for a user.
     *
     * @param int   $id
     * @param int   $userId
     * @param array $data
     *
     * @return Category|null
     */
    public function update(int $id, int $userId, array $data): ?Category;

    /**
     * Delete a category for a user.
     *
     * @param int $id
     * @param int $userId
     *
     * @return bool
     */
    public function delete(int $id, int $userId): bool;
}
