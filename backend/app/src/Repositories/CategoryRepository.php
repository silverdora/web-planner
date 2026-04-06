<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Category;

class CategoryRepository extends Repository implements ICategoryRepository
{
    /**
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
