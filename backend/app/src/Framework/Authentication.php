<?php

namespace App\Framework;

final class Authentication
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public static function login(array $userRow): void
    {
        $_SESSION['user'] = [
            'id' => (int)$userRow['id'],
            'role' => strtolower(trim((string)($userRow['role'] ?? ''))),
            'firstName' => (string)($userRow['firstName'] ?? ''),
            'lastName' => (string)($userRow['lastName'] ?? ''),
            'salonId' => isset($userRow['salonId']) && $userRow['salonId'] !== null
                ? (int)$userRow['salonId']
                : null,
        ];
    }


    public static function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // clear session
        $_SESSION = [];

        // remove session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

}
