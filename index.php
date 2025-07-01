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
    <style>
        
        .btn-outline-dark {
    color: white !important;
    border-color: white !important;
}

.btn-outline-dark:hover {
    background-color: white !important;
    color: black !important;
}

        .card-body {
    background-color: rgba(0, 0, 0, 0.7); /* semi transparan hitam */
    color: white;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.card-body h5 {
    font-weight: bold;
    color: #00eaff; /* warna mencolok biru muda */
}

.card-body p {
    color: #ccc; /* abu-abu terang */
}

body {
    background: url('http://localhost/wp1/gambar/background.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #f8f9fa;
}

.card,
.desc-box {
    background-color: rgba(0, 0, 0, 0.75);
    color: #f8f9fa;
}
</style>

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
                         <a href="deskripsi.php?id=<?= $game['games_id'] ?>">
                                <img src="gambar/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="gambar" style="height: 200px; object-fit: cover;">
                         </a>

                        <div class="card-body">
                            <h5><?= htmlspecialchars($game['title']) ?></h5>
                            <p><?= htmlspecialchars($game['genre']) ?> | Rp<?= number_format($game['price'], 0, ',', '.') ?></p>
                            <form onsubmit="return addToCart(this)" class="add-cart-form">
                                <input type="hidden" name="games_id" value="<?= $game['games_id'] ?>">
                               
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
    
    const formData = new FormData();
    formData.append("games_id", games_id);

    fetch("user/cart_action.php", {
        method: "POST",
        body: formData
    }).then(res => res.text()).then(res => {
        alert(res); // pesan sukses
        updateCartIcon(); // opsional: update tampilan keranjang
    });

    return false;
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
