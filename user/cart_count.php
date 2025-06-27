<?php
session_start();
include "../db.php";

$count = 0;
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['user_id'];
    $result = $conn->query("SELECT SUM(quantity) AS total FROM keranjang WHERE user_id = $user_id");
    $row = $result->fetch_assoc();
    $count = $row['total'] ?? 0;
}

echo json_encode(["count" => $count]);
