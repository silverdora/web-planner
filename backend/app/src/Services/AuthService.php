<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Repositories\IUserRepository;
use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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

    public function login(string $email, string $password): array
    {
        $email = strtolower(trim($email));

        $user = $this->userRepository->getByEmail($email);
        if (!$user) {
            throw new \InvalidArgumentException('Invalid email or password.');
        }

        if (!password_verify($password, (string)$user['password'])) {
            throw new \InvalidArgumentException('Invalid email or password.');
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

    public function validateToken(string $token): bool
    {
        try {
            $decoded = JWT::decode($token, new Key(Config::$secretKey, self::JWT_ALGORITHM));

            // Validate required claims
            if (!isset($decoded->iss) || !isset($decoded->aud) || !isset($decoded->exp)) {
                return false;
            }

            // Validate issuer and audience match domain
            if ($decoded->iss !== Config::$domain || $decoded->aud !== Config::$domain) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false; // Invalid token
        }
    }

    public function getUserFromToken(string $token): ?User
    {
        try {
            $decoded = JWT::decode($token, new Key(Config::$secretKey, self::JWT_ALGORITHM));
        } catch (\Exception $e) {
            return null; // Invalid token
        }

        // Get user by ID from the decoded token
        if (isset($decoded->data->id)) {
            return $this->userRepository->getById($decoded->data->id);
        }

        return null;
    }
}
