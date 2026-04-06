<?php

namespace App\Services;

use App\Models\User;

interface IAuthService
{
    public function register(User $user): User;
    public function authenticate(string $email, string $password): ?User;
    public function generateToken(User $user): string;
}
