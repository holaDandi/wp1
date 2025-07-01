<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ PROSES DARI KERANJANG
    if (isset($_POST['selected_games'], $_POST['metode'])) {
        $metode = $_POST['metode'];
        $selected = $_POST['selected_games'];
        $total = 0;
        $items = [];

        foreach ($selected as $keranjang_id) {
            $keranjang_id = intval($keranjang_id);

            $query = $conn->query("SELECT k.*, g.price, g.title FROM keranjang k 
                                   JOIN games g ON k.games_id = g.games_id 
                                   WHERE k.keranjang_id = $keranjang_id AND k.user_id = $user_id");

            $data = $query->fetch_assoc();

            if ($data) {
                $games_id = $data['games_id'];

                // 🔒 Cek apakah user sudah pernah beli game ini
                $cek = $conn->query("
                    SELECT * FROM item_pemesanan ip 
                    JOIN pemesanan p ON ip.order_id = p.order_id
                    WHERE p.user_id = $user_id AND ip.games_id = $games_id
                ");
                if ($cek->num_rows > 0) {
                    echo "<script>alert('Kamu sudah pernah membeli game \"{$data['title']}\"'); window.location.href = 'cart.php';</script>";
                    exit;
                }

                $subtotal = $data['price'] * $data['quantity'];
                $total += $subtotal;

                $items[] = [
                    'games_id' => $games_id,
                    'quantity' => $data['quantity'],
                    'price' => $data['price']
                ];

                // 🧹 Hapus dari keranjang
                $conn->query("DELETE FROM keranjang WHERE keranjang_id = $keranjang_id AND user_id = $user_id");
            }
        }

        if (!empty($items)) {
            $conn->query("INSERT INTO pemesanan (user_id, total, status, metode) VALUES ($user_id, $total, 'pending', '$metode')");
            $order_id = $conn->insert_id;

            foreach ($items as $item) {
                $conn->query("INSERT INTO item_pemesanan (order_id, games_id, quantity, price)
                              VALUES ($order_id, {$item['games_id']}, {$item['quantity']}, {$item['price']})");
            }
        }

        echo "<script>alert('Pembayaran berhasil!'); window.location.href = '../index.php';</script>";
        exit;

    // ✅ BELI LANGSUNG DARI HALAMAN DESKRIPSI
    } elseif (isset($_POST['games_id'])) {
        $games_id = intval($_POST['games_id']);

        // 🔒 Cek apakah user sudah pernah beli
        $cek = $conn->query("
            SELECT * FROM item_pemesanan ip 
            JOIN pemesanan p ON ip.order_id = p.order_id
            WHERE p.user_id = $user_id AND ip.games_id = $games_id
        ");
        if ($cek->num_rows > 0) {
            echo "<script>alert('Kamu sudah pernah membeli game ini.'); window.location.href = '../index.php';</script>";
            exit;
        }

        // Ambil harga
        $g = $conn->query("SELECT price FROM games WHERE games_id = $games_id")->fetch_assoc();
        $price = $g['price'];

        $conn->query("INSERT INTO pemesanan (user_id, total, status, metode) VALUES ($user_id, $price, 'pending', 'DANA')");
        $order_id = $conn->insert_id;

        $conn->query("INSERT INTO item_pemesanan (order_id, games_id, quantity, price) 
                      VALUES ($order_id, $games_id, 1, $price)");

        echo "<script>alert('Pembelian berhasil!'); window.location.href = '../index.php';</script>";
        exit;
    }
}

// ⛔ fallback kalau request tidak valid
header("Location: cart.php");
exit;
?>
