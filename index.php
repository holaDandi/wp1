<?php
session_start();
include "db.php";
$result = $conn->query("SELECT * FROM games");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- HEADER -->
<div class="container mt-3 mb-4 d-flex justify-content-between align-items-center">
    <div>
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
            <a href="register.php" class="btn btn-outline-success">Register</a>
        <?php else: ?>
            <span>Halo, <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong> (<?= $_SESSION['user']['role'] ?>)</span>
            <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
        <?php endif; ?>
    </div>
    <div>
        <a href="user/cart.php" class="btn btn-outline-dark">
    🛒 Keranjang
    </a>

    </div>
</div>

<!-- GAME LIST -->
<div class="container">
    <h1 class="mb-4">Daftar Game</h1>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($game = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="gambar/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="gambar" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($game['title']) ?></h5>
                            <p><?= htmlspecialchars($game['genre']) ?> | Rp<?= number_format($game['price'], 0, ',', '.') ?></p>
                            <form onsubmit="return addToCart(this)" class="add-cart-form">
                                <input type="hidden" name="games_id" value="<?= $game['games_id'] ?>">
                                <input type="number" name="quantity" value="1" class="form-control mb-2" min="1">
                                <button type="submit" class="btn btn-success w-100">Tambah ke Keranjang</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">Belum ada game tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<!-- SCRIPTS -->
<script>
function addToCart(form) {
    const games_id = form.querySelector('input[name="games_id"]').value;
    const quantity = form.querySelector('input[name="quantity"]').value;

    const formData = new FormData();
    formData.append("games_id", games_id);
    formData.append("quantity", quantity);

    fetch("user/cart.php", {
        method: "POST",
        body: formData
    }).then(res => res.text()).then(res => {
        alert("Berhasil ditambahkan ke keranjang!");
        updateCartIcon();
    });

    return false; // prevent form submit
}

function updateCartIcon() {
    fetch("user/cart_action.php", {
    method: "POST",
    body: formData
})

}

// Update badge saat halaman load
updateCartIcon();
</script>

</body>
</html>
