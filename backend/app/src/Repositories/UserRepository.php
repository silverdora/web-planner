<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\User;
use PDO;

class UserRepository extends Repository implements IUserRepository
{
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

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
