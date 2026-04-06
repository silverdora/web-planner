<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Repositories\IUserRepository;
use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use App\Config;

class AuthService implements IAuthService
{
    private const JWT_ALGORITHM = 'HS256';
    private IUserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function authenticate(string $email, string $password): ?User
    {
        $userData = $this->userRepository->getByEmail($email);
        if (!$userData) {
            return null;
        }
        $user = new User($userData['id'], $userData['full_name'], $userData['email'], $userData['password']);

        // Verify password against hash
        if (!password_verify($password, $user->password)) {
            return null;
        }

        return $user;
    }

    public function register(User $user): User
    {
        // Hash password before storing
        $user->password = password_hash($user->password, PASSWORD_DEFAULT);

        // Check if user already exists
        $existingUser = $this->userRepository->getByEmail($user->email);
        if ($existingUser) {
            throw new UserAlreadyExistsException(); // custom exception
        }

        $this->userRepository->create($user); // create user
        return $user;
    }

    public function generateToken(User $user): string
    {
        $now = time();
        $expiration = $now + (Config::$tokenExpirationHours * 3600); // Convert hours to seconds

        $payload = [
            'iss' => Config::$domain, // Issuer
            'aud' => Config::$domain, // Audience
            'iat' => $now, // Issued at
            'nbf' => $now, // Not before
            'exp' => $expiration, // Expiration time (24 hours from now)
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
        ];

        return JWT::encode($payload, Config::$secretKey, self::JWT_ALGORITHM);
    }
}
