<?php

session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$error = "";


/* =========================
   SIMPAN USER
========================= */

if (isset($_POST['simpan'])) {

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = $_POST['password'];

    $role = mysqli_real_escape_string(
        $koneksi,
        $_POST['role']
    );


    /* =========================
       VALIDASI
    ========================= */

    if ($nama == "") {

        $error = "Nama pengguna wajib diisi.";

    } elseif ($username == "") {

        $error = "Username wajib diisi.";

    } elseif ($password == "") {

        $error = "Password wajib diisi.";

    } elseif (strlen($password) < 6) {

        $error = "Password minimal 6 karakter.";

    } elseif ($role != "admin" && $role != "kasir") {

        $error = "Role pengguna tidak valid.";

    } else {


        /* =========================
           CEK USERNAME
        ========================= */

        $cek = mysqli_query(
            $koneksi,
            "SELECT id_user
             FROM users
             WHERE username='$username'"
        );


        if (mysqli_num_rows($cek) > 0) {

            $error = "Username sudah digunakan.";

        } else {


            /* =========================
               HASH PASSWORD
            ========================= */

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /* =========================
               INSERT USER
            ========================= */

            $query = mysqli_query(
                $koneksi,
                "INSERT INTO users
                (
                    nama,
                    username,
                    password,
                    role
                )
                VALUES
                (
                    '$nama',
                    '$username',
                    '$password_hash',
                    '$role'
                )"
            );


            if ($query) {

                header("Location: user.php");
                exit;

            } else {

                $error = "Data pengguna gagal ditambahkan: "
                       . mysqli_error($koneksi);

            }

        }

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

    <title>Tambah Pengguna | KASIR</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="logo">
            <div class="logo-area">

                <img src="../assets/img/logo.jpg" class="logo-icon">

                <div>
                    <h2>K-Mart</h2>
                    <span>Admin Management System</span>
                </div>

            </div>
        </div>>

        <div class="menu-title">
            Menu Utama
        </div>

        <nav class="menu">

            <a href="index.php">
                <span class="menu-icon">⌂</span>
                <span>Beranda</span>
            </a>

            <a href="produk.php">
                <span class="menu-icon">▣</span>
                <span>Produk</span>
            </a>

            <a href="pelanggan.php">
                <span class="menu-icon">♙</span>
                <span>Pelanggan</span>
            </a>

            <a href="penjualan.php">
                <span class="menu-icon">▤</span>
                <span>Penjualan</span>
            </a>

            <a href="laporan.php">
                <span class="menu-icon">◷</span>
                <span>Laporan</span>
            </a>

            <a href="user.php" class="active">
                <span class="menu-icon">♙</span>
                <span>Users</span>
            </a>
            
        </nav>

    </aside>



<!-- =========================
     MAIN
========================= -->

<main class="main">


    <!-- =========================
         TOPBAR
    ========================= -->

    <header class="topbar">


        <div class="topbar-left">

            <h3>
                Tambah Pengguna
            </h3>

            <span>
                Sistem Pengelolaan Kasir
            </span>

        </div>


        <div class="topbar-right">


            <!-- PROFIL USER -->

            <div class="admin-profile">


                <div class="admin-avatar">

                    <?= strtoupper(
                        substr(
                            $_SESSION['nama'],
                            0,
                            1
                        )
                    ); ?>

                </div>


                <div class="admin-info">

                    <strong>

                        <?= htmlspecialchars(
                            $_SESSION['nama']
                        ); ?>

                    </strong>


                    <small>

                        @<?= htmlspecialchars(
                            $_SESSION['username']
                        ); ?>

                    </small>

                </div>

            </div>


            <!-- LOGOUT -->

            <a
                href="../logout.php"
                class="btn-logout"
                onclick="return confirm('Yakin ingin logout?')"
            >
                Logout
            </a>


        </div>

    </header>



    <!-- =========================
         CONTENT
    ========================= -->

    <section class="content">


        <?php if ($error != ""): ?>

            <div class="alert-error">

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <!-- =========================
             FORM
        ========================= -->

        <div class="form-box">


            <form method="POST">


                <!-- NAMA -->

                <div class="form-group">

                    <label for="nama">
                        Nama Pengguna
                    </label>


                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Contoh: Nama Pengguna"
                        value="<?= isset($_POST['nama'])
                            ? htmlspecialchars($_POST['nama'])
                            : ''; ?>"
                        required
                    >

                </div>



                <!-- USERNAME -->

                <div class="form-group">

                    <label for="username">
                        Username
                    </label>


                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Contoh: kasir01"
                        value="<?= isset($_POST['username'])
                            ? htmlspecialchars($_POST['username'])
                            : ''; ?>"
                        required
                    >

                </div>



                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 6 karakter"
                        minlength="6"
                        required
                    >

                </div>



                <!-- ROLE -->

                <div class="form-group">

                    <label for="role">
                        Role
                    </label>


                    <select
                        id="role"
                        name="role"
                        required
                    >

                        <option value="">
                            -- Pilih Role --
                        </option>


                        <option
                            value="admin"
                            <?= isset($_POST['role']) &&
                                $_POST['role'] == 'admin'
                                ? 'selected'
                                : ''; ?>
                        >
                            Admin
                        </option>


                        <option
                            value="kasir"
                            <?= isset($_POST['role']) &&
                                $_POST['role'] == 'kasir'
                                ? 'selected'
                                : ''; ?>
                        >
                            Kasir
                        </option>

                    </select>

                </div>



                <!-- BUTTON -->

                <div class="form-actions">


                    <a
                        href="user.php"
                        class="btn-cancel"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        name="simpan"
                        class="btn-primary"
                    >
                        Simpan Pengguna
                    </button>


                </div>


            </form>

        </div>


    </section>

</main>


</body>

</html>