<?php
    declare(strict_types = 1);

    function loadEnvironmentVariables() : void {
        $envFile = __DIR__ . '/../../.env';

        if( !is_file($envFile) ) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if( $lines === false ) {
            return;
        }

        foreach( $lines as $line ) {
            $trimmedLine = trim($line);

            if( $trimmedLine === '' || str_starts_with($trimmedLine, '#') ) {
                continue;
            }

            [$name, $value] = array_pad(explode('=', $trimmedLine, 2), 2, '');
            $name = trim($name);
            $value = trim($value);

            if( $name === '' ) {
                continue;
            }

            if( getenv($name) === false ) {
                putenv("{$name}={$value}");
            }
        }
    }

    function getDatabaseConfig() : array {
        loadEnvironmentVariables();

        return [
            'host'     => getenv('DB_HOST')     ?: getenv('MYSQL_HOST')     ?: 'db',
            'dbname'   => getenv('DB_NAME')     ?: getenv('MYSQL_DATABASE') ?: 'wedding_db',
            'user'     => getenv('DB_USER')     ?: getenv('MYSQL_USER')     ?: 'wedding_user',
            'password' => getenv('DB_PASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '',
        ];
    }

    function getDatabaseConnection() : PDO {
        $config = getDatabaseConfig();

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";

        try {
            $db = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false, // Better security for prepared statements
            ]);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            exit("A database connection error occurred. Please try again later.");
        }
        return $db;
    }
?>