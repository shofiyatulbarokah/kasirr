<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: produk.php");
    exit;
}

$id_produk = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk WHERE id_produk='$id_produk'"
);

if (mysqli_num_rows($query) == 0) {
    header("Location: produk.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

$error = "";

if (isset($_POST['update'])) {

    $nama_produk = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_produk']
    );

    $harga = mysqli_real_escape_string(
        $koneksi,
        $_POST['harga']
    );

    $stok = mysqli_real_escape_string(
        $koneksi,
        $_POST['stok']
    );

    if ($nama_produk == "") {
        $error = "Nama produk wajib diisi.";
    } elseif ($harga == "" || $harga < 0) {
        $error = "Harga produk tidak valid.";
    } elseif ($stok == "" || $stok < 0) {
        $error = "Stok produk tidak valid.";
    } else {

        $update = mysqli_query(
            $koneksi,
            "UPDATE produk SET
                nama_produk='$nama_produk',
                harga='$harga',
                stok='$stok'
             WHERE id_produk='$id_produk'"
        );

        if ($update) {
            header("Location: produk.php");
            exit;
        } else {
            $error = "Data produk gagal diperbarui: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Produk - Admin KASIR</title>

    <link rel="stylesheet" href="../assets/css/style.css">

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

        <a href="produk.php" class="active">
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

        <a href="user.php">
            <span class="menu-icon">♙</span>
            <span>Users</span>
        </a>
        
    </nav>

</aside>


<div class="main">

    <header class="topbar">

        <div class="topbar-left">

            <h3>Edit Produk</h3>

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


    <div class="content">


        <div class="form-box">

            <?php if ($error != "") : ?>

                <div class="login-error">
                    <?= htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <form method="POST">

                <div class="form-group">

                    <label>ID Produk</label>

                    <input
                        type="text"
                        value="<?= htmlspecialchars($data['id_produk']); ?>"
                        readonly
                    >

                </div>


                <div class="form-group">

                    <label>Nama Produk</label>

                    <input
                        type="text"
                        name="nama_produk"
                        value="<?= htmlspecialchars($data['nama_produk']); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Harga</label>

                    <input
                        type="number"
                        name="harga"
                        value="<?= htmlspecialchars($data['harga']); ?>"
                        min="0"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Stok</label>

                    <input
                        type="number"
                        name="stok"
                        value="<?= htmlspecialchars($data['stok']); ?>"
                        min="0"
                        required
                    >

                </div>


                <div class="form-action">

                    <a href="produk.php" class="btn-cancel">
                        Batal
                    </a>

                    <button
                        type="submit"
                        name="update"
                        class="btn-primary"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>