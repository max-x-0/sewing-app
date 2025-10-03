<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sewing_orders";

$conn = null;

$temp_conn = @new mysqli($host, $user, $pass, $dbname);


if ($temp_conn && !$temp_conn->connect_error) {
    $temp_conn->set_charset("utf8mb4");
    $conn = $temp_conn;
} else {
    $conn = null;
}
