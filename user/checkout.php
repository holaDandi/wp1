<?php
session_start();
include "../db.php";
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['user']['user_id'];

if (!isset($_POST['selected_games'])) {
    echo "<script>alert('Tidak ada game yang dipilih!'); window.location='cart.php';</script>";
    exit;
}

$selected_ids = implode(",", array_map('intval', $_POST['selected_games']));

// Ambil item dari keranjang yang dipilih
$keranjang = $conn->query("SELECT k.*, g.price, g.games_id FROM keranjang k 
JOIN games g ON k.games_id = g.games_id 
WHERE k.user_id = $user_id AND k.keranjang_id IN ($selected_ids)");

$total = 0;
$items = [];
while ($row = $keranjang->fetch_assoc()) {
    $total += $row['price'] * $row['quantity'];
    $items[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ewallet']) && isset($_POST['nomor'])) {
    $ewallet = $_POST['ewallet'];
    $nomor = $_POST['nomor'];

    // Simpan ke pemesanan
    $conn->query("INSERT INTO pemesanan (user_id, total) VALUES ($user_id, $total)");
    $order_id = $conn->insert_id;

    // Simpan item pemesanan
    foreach ($items as $i) {
        $g = $i['games_id'];
        $q = $i['quantity'];
        $p = $i['price'];
        $conn->query("INSERT INTO item_pemesanan (order_id, games_id, quantity, price) VALUES ($order_id, $g, $q, $p)");
    }

    // Hapus dari keranjang yang dipilih
    $conn->query("DELETE FROM keranjang WHERE keranjang_id IN ($selected_ids)");

    echo "<script>alert('Pembayaran berhasil menggunakan $ewallet (No: $nomor)'); window.location='orders.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Checkout</h2>
    <p>Total: <strong>Rp<?= number_format($total, 0, ',', '.') ?></strong></p>

    <form method="POST" onsubmit="return confirm('Lanjutkan pembayaran dengan E-Wallet ini?')">
        <!-- Kirim ulang data selected_games[] -->
        <?php foreach ($_POST['selected_games'] as $id): ?>
            <input type="hidden" name="selected_games[]" value="<?= intval($id) ?>">
        <?php endforeach; ?>

        <label class="form-label">Pilih E-Wallet:</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="ewallet" value="GoPay" required>
            <label class="form-check-label">GoPay</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="ewallet" value="OVO" required>
            <label class="form-check-label">OVO</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="ewallet" value="DANA" required>
            <label class="form-check-label">DANA</label>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="radio" name="ewallet" value="ShopeePay" required>
            <label class="form-check-label">ShopeePay</label>
        </div>

        <input type="number" class="form-control mb-3" name="nomor" placeholder="Nomor HP" required>
        <button class="btn btn-success">Bayar</button>
    </form>
</div>
</body>
</html>
