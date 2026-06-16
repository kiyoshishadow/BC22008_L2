<?php

namespace App\Config;

use PDO;
use PDOException;

class DB
{
    public function connect(): PDO
    {
        // Si hay variables de entorno (por ejemplo en Render), úsalas.
        // En entorno local, cargamos el archivo .env mediante phpdotenv si está disponible.
        $projectRoot = dirname(__DIR__, 2);

        if ((getenv('DB_HOST') === false || getenv('DB_HOST') === '') && class_exists('\Dotenv\Dotenv')) {
            try {
                $dotenv = \Dotenv\Dotenv::createImmutable($projectRoot);
                $dotenv->safeLoad();
            } catch (\Throwable $e) {
                // safeLoad no debe lanzar, pero en caso de error continuamos con valores por defecto
            }
        }

        $host = getenv('DB_HOST') !== false && getenv('DB_HOST') !== '' ? getenv('DB_HOST') : ($_ENV['DB_HOST'] ?? '127.0.0.1');
        $db   = getenv('DB_NAME') !== false && getenv('DB_NAME') !== '' ? getenv('DB_NAME') : ($_ENV['DB_NAME'] ?? 'bc22008_parcial3');
        $user = getenv('DB_USER') !== false && getenv('DB_USER') !== '' ? getenv('DB_USER') : ($_ENV['DB_USER'] ?? 'root');
        $pass = getenv('DB_PASS') !== false && getenv('DB_PASS') !== '' ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}
