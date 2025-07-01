<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: cart.php");
    exit;
}

$selected_games = [];

if (isset($_POST['selected_games'])) {
    $selected_games = $_POST['selected_games'];
} elseif (isset($_POST['games_id'])) {
    $selected_games[] = $_POST['games_id']; // dari tombol "Beli Sekarang"
} else {
    header("Location: cart.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Pilih Metode Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Pilih Dompet Digital</h3>

    <form method="POST" action="checkout.php">
        <?php foreach ($selected_games as $id): ?>
            <input type="hidden" name="selected_games[]" value="<?= htmlspecialchars($id) ?>">
        <?php endforeach; ?>

        <div class="mb-3">
            <label for="metode" class="form-label">Dompet Digital</label>
            <select name="metode" id="metode" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="DANA">DANA</option>
                <option value="GOPAY">GOPAY</option>
                <option value="OVO">OVO</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Lanjutkan Pembayaran</button>
        <a href="cart.php" class="btn btn-secondary">← Kembali</a>
    </form>
</div>
</body>
</html>
