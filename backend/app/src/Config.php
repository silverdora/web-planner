<?php
namespace App;

/**
 * Application configuration values and environment initialization.
 */
class Config
{
    public const DB_SERVER_NAME = "mysql";
    public const DB_USERNAME = "root";
    public const DB_PASSWORD = "secret123";
    public const DB_NAME = "planner";

    /** @var string Secret key used for JWT signing. */
    public static string $secretKey;

    /** @var string Application domain / base URL. */
    public static string $domain;

    /** @var int Token expiration time in hours. */
    public static int $tokenExpirationHours;

    /**
     * Initialize configuration from environment variables.
     *
     * @return void
     */
    public static function init(): void
    {
        self::$secretKey = getenv('SECRET_KEY') ?: $_ENV['SECRET_KEY'] ?? 'your-secret-key-change-this-in-production';
        self::$domain = getenv('DOMAIN') ?: $_ENV['DOMAIN'] ?? 'http://localhost';
        self::$tokenExpirationHours = (int)(getenv('TOKEN_EXPIRATION_HOURS') ?: $_ENV['TOKEN_EXPIRATION_HOURS'] ?? 24);
    }
}
Config::init();