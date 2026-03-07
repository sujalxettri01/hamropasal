<?php
// Centralized app configuration.
define('APP_NAME', 'HamroPasal');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', APP_ENV !== 'production');
define('APP_BASE_URL', rtrim(getenv('APP_BASE_URL') ?: '/hamropasal', '/'));

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'hamropasal');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
?>
