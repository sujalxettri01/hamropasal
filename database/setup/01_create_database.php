<?php
$servername = "localhost";
$username = "root";
$password = "";

$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS hamropasal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Database ready: hamropasal";
} else {
    die("Cannot create database: " . mysqli_error($conn));
}

mysqli_close($conn);
?>
