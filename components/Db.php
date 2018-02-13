<?php

namespace App\components;


use PDO;
use PDOException;

/**
 * Class Db is used to interact with database.
 */
class Db
{
    /**
     * @var object $connection PDO connection object
     */
    private static $connection = null;

    /**
     * Utilizes the database connection
     *
     * @throws PDOException whether there isn't database connection
     * @return PDO object
     */
    public static function getConnection(): PDO
    {
        if(self::$connection === null) {
            try {
                $params = require ROOT.'/config/db_params.php';
                $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}";
                $opt = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ];
                self::$connection = new PDO($dsn, $params['user'], $params['password'], $opt);
            } catch (PDOException $ex) {
                throw new PDOException("Database connection failed: ".$ex->getMessage());
            }
        }

        return self::$connection;
    }

    /**
     * Disables object creation
     */
    private function __construct() {}

    /**
     * Disallows object deserialization
     */
    private function __wakeup() {}
    //

    /**
     * Disallows object cloning
     */
    private function __clone() {}
}
