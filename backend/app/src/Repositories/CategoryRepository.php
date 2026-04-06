<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Category;

class CategoryRepository extends Repository implements ICategoryRepository
{
    /**
     * Get all categories owned by the given user.
     *
     * @param int $userId
     *
     * @return Category[]
     */
    public function getByUserId(int $userId): array
    {
        $sql = 'SELECT id, user_id, name
                FROM categories
                WHERE user_id = :user_id
                ORDER BY name ASC';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($rows as $row) {
            $categories[] = new Category($row);
        }

        return $categories;
    }

    /**
     * Get all categories.
     *
     * @return Category[]
     */
    public function getAll(): array
    {
        $sql = 'SELECT id, user_id, name FROM categories';
        $stmt = $this->getConnection()->query($sql);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($rows as $row) {
            $categories[] = new Category($row);
        }

        return $categories;
    }

    /**
     * Get a category by id.
     *
     * @param int $id
     *
     * @return Category|null
     */
    public function getById(int $id): ?Category
    {
        $sql = 'SELECT id, user_id, name
            FROM categories
            WHERE id = :id';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new Category($row) : null;
    }

    /**
     * Get a category by id constrained to a user id.
     *
     * @param int $id
     * @param int $userId
     *
     * @return Category|null
     */
    public function getByIdAndUserId(int $id, int $userId): ?Category
    {
        $sql = 'SELECT id, user_id, name
                FROM categories
                WHERE id = :id
                  AND user_id = :user_id';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new Category($row) : null;
    }

    /**
     * Create a new category.
     *
     * @param Category $category
     *
     * @return Category
     */
    public function create(Category $category): Category
    {
        $sql = 'INSERT INTO categories (user_id, name)
                VALUES (:user_id, :name)';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([
            ':user_id' => $category->userId,
            ':name' => $category->name,
        ]);

        $category->id = (int)$this->getConnection()->lastInsertId();

        return $category;
    }

    /**
     * Update a category for a specific user.
     *
     * @param Category $category
     *
     * @return bool
     */
    public function updateByUserId(Category $category): bool
    {
        $sql = 'UPDATE categories
                SET name = :name
                WHERE id = :id
                  AND user_id = :user_id';

        $stmt = $this->getConnection()->prepare($sql);

        return $stmt->execute([
            ':id' => $category->id,
            ':user_id' => $category->userId,
            ':name' => $category->name,
        ]);
    }

    /**
     * Delete a category owned by a specific user.
     *
     * @param int $id
     * @param int $userId
     *
     * @return void
     */
    public function deleteByUserId(int $id, int $userId): void
    {
        $sql = 'DELETE FROM categories
                WHERE id = :id
                  AND user_id = :user_id';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
