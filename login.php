<?php
session_start();
include "koneksi.php";

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = mysqli_real_escape_string(
        $koneksi,
        $_POST['password']
    );

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM users
         WHERE username='$username'
         LIMIT 1"
    );

    if (mysqli_num_rows($query) == 1) {

        $data = mysqli_fetch_assoc($query);

        if ($password == $data['password']) {

            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];


            /* ADMIN */

            if ($data['role'] == 'admin') {

                header("Location: admin/index.php");
                exit;

            }


            /* KASIR */

            elseif ($data['role'] == 'kasir') {

                header("Location: kasir/index.php");
                exit;

            }

        } else {

            $error = "Password salah.";

        }

    } else {

        $error = "Username tidak ditemukan.";

    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | Kasir</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body class="login-page">


<div class="login-box">

    <div class="login-logo">

        <div>
            <img src="assets/img/logo-removebg-preview.png" class="login-icon">
        </div>

        <h1>K-Mart</h1>

        <p>Management System</p>

    </div>


    <div class="login-title">

        <h2>Masuk ke Sistem</h2>

        <span>
            Silakan masuk menggunakan akun Anda.
        </span>

    </div>


    <?php if ($error != ""): ?>

        <div class="login-error">
            <?= $error; ?>
        </div>

    <?php endif; ?>


    <form method="POST">


        <div class="login-form-group">

            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Masukkan username"
                required
            >

        </div>


        <div class="login-form-group">

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

        </div>


        <button
            type="submit"
            name="login"
            class="login-button"
        >
            Masuk
        </button>


    </form>

</div>


</body>
</html>