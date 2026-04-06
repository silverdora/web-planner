<?php

/** 
 * Base repository class to manage database connections.
 */

namespace App\Framework;

use App\Config;
use PDO;
class Repository
{
    /**
     * Shared PDO connection instance.
     *
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    /**
     * Get (and lazily initialize) the PDO connection.
     *
     * @return PDO
     */
    protected function  getConnection(): PDO
    {
        if (self::$connection === null) {
            $this->connect();
        }
        return self::$connection;
    }

    /**
     * Establish the database connection.
     *
     * @return void
     */
    private function connect(): void
    {
        try {
            $connectionString = 'mysql:host=' . Config::DB_SERVER_NAME . ';dbname=' . Config::DB_NAME . ';charset=utf8mb4';

            self::$connection = new PDO(
                $connectionString,
                Config::DB_USERNAME,
                Config::DB_PASSWORD
            );

            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            die('DB connection failed');
        }
    }
}
?>