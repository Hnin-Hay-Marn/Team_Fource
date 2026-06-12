<?php

function getDbPath(): string
{
    return __DIR__ . '/database.sqlite';
}

function getDb(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $path = getDbPath();
        $dsn = "sqlite:$path";
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
    }

    return $pdo;
}

function initializeDatabase(): void
{
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    if ($schema === false) {
        throw new RuntimeException('Could not read schema.sql');
    }

    getDb()->exec($schema);
}

// Ensure the database is initialized when this file is included.
initializeDatabase();
