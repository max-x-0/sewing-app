<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sewing_orders";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
