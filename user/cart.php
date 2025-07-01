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
    <form method="POST" action="metode_pembayaran.php">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;">Pilih</th>
                    <th style="width: 40%;">Game</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 20%;">Harga</th>
                    <th style="width: 15%;">Aksi</th>
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
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="<?= $item['keranjang_id'] ?>">Hapus</button>
                        </td>
                    </tr>
                    <?php $total += $subtotal; ?>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h5>Total Potensial: Rp<?= number_format($total, 0, ',', '.') ?></h5>
        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary">Bayar yang Dipilih</button>
            <a href="../index.php" class="btn btn-secondary">← Kembali ke Beranda</a>
        </div>
    </form>
</div>

<!-- Script DELETE -->
<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        if (!confirm("Yakin ingin menghapus game ini dari keranjang?")) return;

        const keranjangId = btn.dataset.id;

        fetch('cart_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=delete&keranjang_id=${keranjangId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Game berhasil dihapus dari keranjang!');
                location.reload();
            } else {
                alert('Gagal menghapus item.');
            }
        });
    });
});
</script>

</body>
</html>
