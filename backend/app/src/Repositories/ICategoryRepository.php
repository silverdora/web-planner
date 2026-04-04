<?php

namespace App\Repositories;

use App\Models\Category;

interface ICategoryRepository
{
    /**
     * @return Category[]
     */
    public function getByUserId(int $userId): array;

    /**
     * @return Category[]
     */
    public function getAll(): array;

    public function getById(int $id): ?Category;

    public function getByIdAndUserId(int $id, int $userId): ?Category;

    public function create(Category $category): Category;

    public function updateByUserId(Category $category): bool;

    public function deleteByUserId(int $id, int $userId): void;
}
