<?php

namespace App\Services;

use App\Models\Category;

interface ICategoryService
{
    public function getAllByUserId(int $userId): array;
    public function getByIdAndUserId(int $id, int $userId): ?Category;
    public function create(int $userId, array $data): Category;
    public function update(int $id, int $userId, array $data): ?Category;
    public function delete(int $id, int $userId): bool;
}
