<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$error = "";

if (isset($_POST['simpan'])) {

    $nama_pelanggan = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_pelanggan']
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );

    $no_hp = mysqli_real_escape_string(
        $koneksi,
        $_POST['no_hp']
    );


    /* VALIDASI */

    if ($nama_pelanggan == "") {

        $error = "Nama pelanggan wajib diisi.";

    } else {

        $query = mysqli_query(
            $koneksi,
            "INSERT INTO pelanggan
            (nama_pelanggan, alamat, no_hp)
            VALUES
            ('$nama_pelanggan', '$alamat', '$no_hp')"
        );


        if ($query) {

            header("Location: pelanggan.php");
            exit;

        } else {

            $error = "Data pelanggan gagal ditambahkan: "
                   . mysqli_error($koneksi);

        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Tambah Pelanggan - Admin KASIR</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

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
    </div>

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

        <a href="pelanggan.php" class="active">
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

        <a href="user.php">
            <span class="menu-icon">♙</span>
            <span>Users</span>
        </a>
        
    </nav>

</aside>


<!-- MAIN -->

<main class="main">


    <!-- TOPBAR -->

<header class="topbar">

        <div class="topbar-left">

            <h3>Tambah Pelanggan</h3>

            <span>Sistem Pengelolaan Kasir</span>

        </div>


        <div class="topbar-right">


            <!-- PROFIL USER -->

            <div class="admin-profile">

                <div class="admin-avatar">

                    <?= strtoupper(
                        substr($_SESSION['nama'], 0, 1)
                    ); ?>

                </div>


                <div class="admin-info">

                    <strong>
                        <?= htmlspecialchars($_SESSION['nama']); ?>
                    </strong>

                    <small>
                        @<?= htmlspecialchars($_SESSION['username']); ?>
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



    <!-- CONTENT -->

    <section class="content">

        <?php if ($error != ""): ?>

            <div class="alert-error">
                <?= $error; ?>
            </div>

        <?php endif; ?>


        <!-- FORM -->

        <div class="form-box">

            <form method="POST">


                <!-- NAMA PELANGGAN -->

                <div class="form-group">

                    <label for="nama_pelanggan">
                        Nama Pelanggan
                    </label>

                    <input
                        type="text"
                        id="nama_pelanggan"
                        name="nama_pelanggan"
                        placeholder="Contoh: Budi Santoso"
                        value="<?= isset($_POST['nama_pelanggan']) ? htmlspecialchars($_POST['nama_pelanggan']) : ''; ?>"
                        required
                    >

                </div>



                <!-- ALAMAT -->

                <div class="form-group">

                    <label for="alamat">
                        Alamat
                    </label>

                    <textarea
                        id="alamat"
                        name="alamat"
                        placeholder="Masukkan alamat pelanggan"
                        rows="4"
                    ><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>

                </div>



                <!-- NO HP -->

                <div class="form-group">

                    <label for="no_hp">
                        No. HP
                    </label>

                    <input
                        type="text"
                        id="no_hp"
                        name="no_hp"
                        placeholder="Contoh: 081234567890"
                        maxlength="20"
                        value="<?= isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>"
                    >

                </div>



                <!-- BUTTON -->

                <div class="form-actions">

                    <a
                        href="pelanggan.php"
                        class="btn-cancel"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        name="simpan"
                        class="btn-primary"
                    >
                        Simpan Pelanggan
                    </button>

                </div>


            </form>

        </div>


    </section>

</main>

</body>
</html>