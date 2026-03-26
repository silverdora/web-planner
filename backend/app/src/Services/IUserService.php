<?php

namespace App\Services;

use App\Models\User;

interface IUserService
{
    public function getById(int $id): ?User;
    public function getByEmail(string $email): ?array;
    public function create(array $user): User;
    public function update(User $user): bool;
    public function delete(int $id): bool;
}
