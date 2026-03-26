<?php

namespace App\Services;

use App\Models\User;

interface IAuthService
{
    public function login(string $email, string $password): array;
    public function register(User $user): User;
}
