<?php
session_start();
include "../db.php";

// Cek login & role
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login.php");
    exit;
}
if ($_SESSION['user']['role'] !== 'admin') {
    echo "<div style='margin: 50px; font-family: sans-serif'><h3>Akses ditolak ❌</h3><p>Halaman ini hanya untuk admin.</p></div>";
    exit;
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $conn->real_escape_string($_POST['description']);

    // Gambar
    $imageName = $conn->real_escape_string($_FILES['image']['name']);
    $imageTmp = $_FILES['image']['tmp_name'];
    $uploadPath = "../gambar/" . basename($imageName);

    if (move_uploaded_file($imageTmp, $uploadPath)) {
        $sql = "INSERT INTO games (title, genre, price, stock, description, image)
                VALUES ('$title', '$genre', $price, $stock, '$desc', '$imageName')";
        if ($conn->query($sql)) {
            header("Location: games_crud.php");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Gagal menyimpan: " . $conn->error . "</div>";
        }
    } else {
        echo "<script>alert('Upload gambar gagal!');</script>";
    }
}

$games = $conn->query("SELECT * FROM games");
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Game</h2>
    <form method="POST" enctype="multipart/form-data" onsubmit="return validateGameForm()">
        <input class="form-control my-1" name="title" placeholder="Judul" required>
        <input class="form-control my-1" name="genre" placeholder="Genre" required>
        <input class="form-control my-1" name="price" type="number" id="price" placeholder="Harga" required>
        <input class="form-control my-1" name="stock" type="number" placeholder="Stok" required>
        <textarea class="form-control my-1" name="description" placeholder="Deskripsi" required></textarea>
        <input type="file" class="form-control my-1" name="image" accept="image/*" required>
        <button class="btn btn-primary mt-2">Simpan</button>
    </form>

    <hr>
    <h3>Daftar Game</h3>
    <table class="table table-striped">
        <thead><tr><th>Gambar</th><th>Judul</th><th>Genre</th><th>Harga</th><th>Stok</th></tr></thead>
        <tbody>
        <?php while($g = $games->fetch_assoc()): ?>
        <tr>
            <td><img src="../gambar/<?= htmlspecialchars($g['image']) ?>" width="80"></td>
            <td><?= htmlspecialchars($g['title']) ?></td>
            <td><?= htmlspecialchars($g['genre']) ?></td>
            <td>Rp<?= number_format($g['price'], 0) ?></td>
            <td><?= $g['stock'] ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function validateGameForm() {
    const price = document.getElementById("price").value;
    if (price <= 0) {
        alert("Harga harus lebih dari 0!");
        return false;
    }
    return true;
}
</script>
</body>
</html>
