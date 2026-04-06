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

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email
     * @param string $password
     *
     * @return User|null Returns the authenticated user or null on failure.
     */
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

    /**
     * Register a new user.
     *
     * Hashes the password and persists the user, throwing if the
     * email already exists.
     *
     * @param User $user
     *
     * @return User
     *
     * @throws UserAlreadyExistsException
     */
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

    /**
     * Generate a JWT for the given user.
     *
     * @param User $user
     *
     * @return string Encoded JWT token.
     */
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
