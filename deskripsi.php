<?php
session_start();
include "db.php";

if (!isset($_GET['id'])) {
    echo "Game tidak ditemukan.";
    exit;
}

$games_id = intval($_GET['id']);
$game = $conn->query("SELECT * FROM games WHERE games_id = $games_id")->fetch_assoc();


if (!$game) {
    echo "Data game tidak valid.";
    exit;
}

$purchased = false;
if (isset($_SESSION['user'])) {
    $uid = $_SESSION['user']['user_id'];
    $check = $conn->query("
        SELECT * FROM item_pemesanan ip 
        JOIN pemesanan p ON ip.order_id = p.order_id
        WHERE ip.games_id = $games_id AND p.user_id = $uid
    ");
    if ($check->num_rows > 0) {
        $purchased = true;
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <style>

        img.shadow-sm:hover {
    transform: scale(1.03);
    transition: 0.3s ease;
    box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
}

body {
    background: url('http://localhost/wp1/gambar/background.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #f8f9fa;
}

.card,
.desc-box {
    background-color: rgba(0, 0, 0, 0.75);
    color: ;
}
</style>

    <title><?= htmlspecialchars($game['title']) ?> - Detail Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .game-image { width: 100%; border-radius: 8px; object-fit: cover; }
        .desc-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ddd; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-5">
            <img src="gambar/<?= htmlspecialchars($game['image']) ?>" class="game-image" alt="<?= $game['title'] ?>">
        </div>
        <div class="col-md-7">
            <div class="desc-box">
                <h2><?= htmlspecialchars($game['title']) ?></h2>
                <p><strong>Genre:</strong> <?= htmlspecialchars($game['genre']) ?></p>
                <p><strong>Harga:</strong> Rp<?= number_format($game['price'], 0, ',', '.') ?></p>
                <p><?= nl2br(htmlspecialchars($game['description'])) ?></p>

                <hr>
<h5>Cuplikan Game</h5>
<div class="row">
    <div class="col-md-4 mb-3">
        <img src="gambar/assasin1.jpg" class="img-fluid rounded shadow-sm" alt="screenshot 1">
    </div>
    <div class="col-md-4 mb-3">
        <img src="gambar/assasin2.jpg" class="img-fluid rounded shadow-sm" alt="screenshot 2">
    </div>
    <div class="col-md-4 mb-3">
        <img src="gambar/assasin3.jpg" class="img-fluid rounded shadow-sm" alt="screenshot 3">
    </div>
</div>


                <hr>
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if (!$purchased): ?>
                        <!-- Tombol tambah ke keranjang -->
                        <form method="POST" action="user/cart_action.php" class="d-inline" onsubmit="return addToCart(this)">
                            <input type="hidden" name="games_id" value="<?= $game['games_id'] ?>">
                            <button type="submit" class="btn btn-success me-2">Tambah ke Keranjang</button>
                        </form>

                        <!-- Tombol checkout langsung -->
                        <form method="POST" action="user/metode_pembayaran.php" class="d-inline">
                            <input type="hidden" name="games_id" value="<?= $game['games_id'] ?>">
                            <button type="submit" class="btn btn-primary">Beli Sekarang</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3">Kamu sudah membeli game ini.</div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-danger">Login dulu untuk membeli game.</p>
                    <a href="login.php" class="btn btn-outline-primary">Login</a>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-secondary">Kembali ke Daftar Game</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addToCart(form) {
    const formData = new FormData(form);
    fetch("user/cart_action.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(res => {
        alert(res);
    });
    return false;
}
</script>

</body>
</html>
