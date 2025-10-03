<?php

use mysqli_sql_exception;

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sewing_orders";

$conn = null;

try {
    $temp_conn = new mysqli($host, $user, $pass, $dbname);

    if ($temp_conn->connect_error) {
        $conn = null;
        $temp_conn->close();
    } else {

        $temp_conn->set_charset("utf8mb4");
        $conn = $temp_conn;
    }
} catch (mysqli_sql_exception $e) {
    error_log("DB Connection failed on remote host: " . $e->getMessage());
    $conn = null;
}
