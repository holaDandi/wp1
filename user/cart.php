<?php
session_start();
include "../db.php";
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$items = $conn->query("SELECT k.*, g.title, g.price FROM keranjang k 
JOIN games g ON k.games_id = g.games_id WHERE k.user_id = $user_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Keranjang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Keranjang</h2>
    <form method="POST" action="checkout.php">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Pilih</th>
                    <th>Game</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; while($item = $items->fetch_assoc()): ?>
                    <?php $subtotal = $item['price'] * $item['quantity']; ?>
                    <tr>
                        <td><input type="checkbox" name="selected_games[]" value="<?= $item['keranjang_id'] ?>"></td>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                    <?php $total += $subtotal; ?>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h5>Total Potensial: Rp<?= number_format($total, 0, ',', '.') ?></h5>
        <button class="btn btn-primary">Bayar yang Dipilih</button>
        <a href="../index.php" class="btn btn-secondary mt-4">← Kembali ke Beranda</a>

    </form>
</div>
</body>
</html>
