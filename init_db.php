<?php
require_once __DIR__ . '/db.php';

echo "Initializing database...\n";
initializeDatabase();
echo "Database initialized at " . getDbPath() . "\n";
