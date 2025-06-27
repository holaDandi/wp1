<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;

        // Cek role dan redirect sesuai
        if ($user['role'] === 'admin') {
            $redirect = $_SESSION['redirect_to'] ?? 'admin/games_crud.php';
        } else {
            $redirect = $_SESSION['redirect_to'] ?? 'index.php';
        }

        unset($_SESSION['redirect_to']);
        header("Location: $redirect");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    <form method="POST">
        <input class="form-control my-2" name="username" placeholder="Username" required>
        <input class="form-control my-2" name="password" type="password" placeholder="Password" required>
        <button class="btn btn-primary" type="submit">Login</button>
    </form>
</div>
</body>
</html>
