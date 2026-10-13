<?php
/**
 * Global Connect - Database Configuration
 * Supports MySQL & SQLite PDO drivers seamlessly
 */

return [
    // Preferred driver: 'mysql' or 'sqlite'
    'driver' => getenv('DB_DRIVER') ?: 'sqlite',

    // MySQL Configuration
    'mysql' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'globalconnect_db',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],

    // SQLite Configuration (Default for standalone/sandbox execution)
    'sqlite' => [
        'path' => __DIR__ . '/../data/globalconnect.db',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    ]
];
