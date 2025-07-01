<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user'])) {
    echo "Unauthorized";
    exit;
}

// 🔥 Hapus item dari keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $keranjang_id = intval($_POST['keranjang_id']);
    $user_id = $_SESSION['user']['user_id'];

    $conn->query("DELETE FROM keranjang WHERE keranjang_id = $keranjang_id AND user_id = $user_id");

    echo json_encode(['status' => 'success']);
    exit;
}

// ✅ Tambah ke keranjang hanya jika game belum ada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['games_id'])) {
    $games_id = intval($_POST['games_id']);
    $user_id = $_SESSION['user']['user_id'];

    $cekBeli = $conn->query("
            SELECT * FROM item_pemesanan ip 
            JOIN pemesanan p ON ip.order_id = p.order_id 
            WHERE p.user_id = $user_id AND ip.games_id = $games_id
        ");


    $cek = $conn->query("SELECT * FROM keranjang WHERE user_id = $user_id AND games_id = $games_id");

    if ($cek->num_rows > 0) {
        echo "Game ini sudah ada di keranjang!";
        exit;
    } else {
        $conn->query("INSERT INTO keranjang (user_id, games_id, quantity) VALUES ($user_id, $games_id, 1)");
        echo "Berhasil ditambahkan";
        exit;
    }
}
?>
