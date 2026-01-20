<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

abstract class DbConnect
{
    protected static ?PDO $connection = null;
    protected const DB_HOST = 'localhost';
    protected const DB_NAME = 'coursportfolio';
    protected const DB_USER = 'root';
    protected const DB_PASS = '';
    
    protected static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=utf8mb4',
                    self::DB_HOST, // 'localhost'
                    self::DB_NAME // 'coursportfolio'
                );
                self::$connection = new PDO(
                    $dsn,
                    self::DB_USER, // 'root',
                    self::DB_PASS, // '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die('Erreur de connexion : ' . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
