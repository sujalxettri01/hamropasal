<?php
require_once __DIR__ . '/../config/app.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int) DB_PORT);

if (!$conn) {
    // Avoid exposing internals in production responses.
    error_log('Database connection failed: ' . mysqli_connect_error());
    if (APP_DEBUG) {
        die('Database connection failed: ' . mysqli_connect_error());
    }
    die('Service temporarily unavailable. Please try again later.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
