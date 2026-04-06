<?php

namespace App\Services;

use App\Models\User;

interface IAuthService
{
    /**
     * Register a new user.
     *
     * @param User $user
     *
     * @return User
     */
    public function register(User $user): User;

    /**
     * Authenticate a user using email and password.
     *
     * @param string $email
     * @param string $password
     *
     * @return User|null
     */
    public function authenticate(string $email, string $password): ?User;

    /**
     * Generate a JWT token for the given user.
     *
     * @param User $user
     *
     * @return string
     */
    public function generateToken(User $user): string;
}
