<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\ICategoryRepository;
use InvalidArgumentException;

class CategoryService implements ICategoryService
{
    private ICategoryRepository $categoryRepository;
    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * @return Category[]
     */
    public function getByUserId(int $userId): array
    {
        return $this->categoryRepository->getByUserId($userId);
    }

    public function getByIdAndUserId(int $id, int $userId): ?Category
    {
        return $this->categoryRepository->getByIdAndUserId($id, $userId);
    }

    public function create(int $userId, array $data): Category
    {
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            throw new InvalidArgumentException('Category name is required.');
        }

        $category = new Category([
            'user_id' => $userId,
            'name' => $name,
        ]);

        return $this->categoryRepository->create($category);
    }

    public function update(int $id, int $userId, array $data): ?Category
    {
        $category = $this->categoryRepository->getByIdAndUserId($id, $userId);

        if (!$category) {
            return null;
        }

        $name = trim($data['name'] ?? '');

        if ($name === '') {
            throw new InvalidArgumentException('Category name is required.');
        }

        $category->name = $name;

        $updated = $this->categoryRepository->updateByUserId($category);

        if (!$updated) {
            return null;
        }

        return $category;
    }

    public function delete(int $id, int $userId): bool
    {
        $category = $this->categoryRepository->getByIdAndUserId($id, $userId);

        if (!$category) {
            return false;
        }

        $this->categoryRepository->deleteByUserId($id, $userId);

        return true;
    }
}
