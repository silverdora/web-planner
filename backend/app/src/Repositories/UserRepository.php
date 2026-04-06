<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\User;
use PDO;

class UserRepository extends Repository implements IUserRepository
{
    /**
     * Get a user entity by its id.
     *
     * @param int $id
     *
     * @return User|null
     */
    public function getById(int $id): ?User
    {
        $sql = 'SELECT id, full_name, email, password
                FROM users
                WHERE id = :id';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\User');
        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Find a user row by email.
     *
     * @param string $email
     *
     * @return array|null Associative array for the row or null if not found.
     */
    public function getByEmail(string $email): ?array
    {
        $sql = 'SELECT id, full_name, email, password
                FROM users
                WHERE email = :email
                LIMIT 1';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Persist a new user record.
     *
     * @param User $userData
     *
     * @return void
     */
    public function create(User $userData): void
    {
        $sql = 'INSERT INTO users (full_name, email, password)
                VALUES (:name, :email, :password)';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([
            ':name' => $userData->name,
            ':email' => $userData->email,
            ':password' => $userData->password,
        ]);
        $userData->id = (int)$this->getConnection()->lastInsertId();
    }

    /**
     * Update an existing user record.
     *
     * @param int  $id
     * @param User $user
     *
     * @return void
     */
    public function update(int $id, User $user): void
    {
        $sql = 'UPDATE users
                SET full_name = :name,
                    email = :email,
                    password = :password
                WHERE id = :id';

        $stmt = $this->getConnection()->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':name' => $user->name,
            ':email' => $user->email,
            ':password' => $user->password
        ]);
    }

    /**
     * Delete a user record by id.
     *
     * @param int $id
     *
     * @return void
     */
    public function delete(int $id): void
    {
        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
