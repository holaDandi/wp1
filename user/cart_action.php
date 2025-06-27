<?php
session_start();
include "../db.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['user_id'];
    $games_id = isset($_POST['games_id']) ? intval($_POST['games_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if ($games_id > 0 && $quantity > 0) {
        // Cek apakah game sudah ada di keranjang
        $check = $conn->query("SELECT * FROM keranjang WHERE user_id=$user_id AND games_id=$games_id");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE keranjang SET quantity = quantity + $quantity WHERE user_id=$user_id AND games_id=$games_id");
        } else {
            $insert = $conn->query("INSERT INTO keranjang (user_id, games_id, quantity) VALUES ($user_id, $games_id, $quantity)");
            if (!$insert) {
                echo "Insert error: " . $conn->error;
                exit;
            }   error_reporting(E_ALL);
                ini_set('display_errors', 1);

        }

        echo "OK";
    } else {
        http_response_code(400);
        echo "Invalid games_id or quantity";
    }
} else {
    http_response_code(405);
    echo "Method not allowed";
}
