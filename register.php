<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $pass = $_POST['password'];
    $email = $_POST['email'];

    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check->num_rows > 0) {
        $error = "Username sudah dipakai!";
    } else {
        $conn->query("INSERT INTO users (username, password, email) VALUES ('$username', '$pass', '$email')");
        header("Location: login.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Register</h2>
    <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    <form method="POST" onsubmit="return validateForm()">
        <input class="form-control my-2" name="username" placeholder="Username" required>
        <input class="form-control my-2" name="email" type="email" placeholder="Email" required>
        <input class="form-control my-2" name="password" type="password" placeholder="Password" required id="pass">
        <input class="form-control my-2" type="password" placeholder="Ulangi Password" required id="pass2">
        <button class="btn btn-success" type="submit">Daftar</button>
    </form>
</div>

<script>
function validateForm() {
    let pass = document.getElementById('pass').value;
    let pass2 = document.getElementById('pass2').value;
    if (pass !== pass2) {
        alert("Password tidak sama!");
        return false;
    }
    return true;
}
</script>
</body>
</html>
