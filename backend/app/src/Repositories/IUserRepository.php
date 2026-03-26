<?php

namespace App\Repositories;

use App\Models\User;

interface IUserRepository
{
    public function getById(int $id): ?User;
    public function getByEmail(string $email): ?array;
    public function create(array $data): User;
    public function update(int $id, User $user): void;
    public function delete(int $id): void;
}