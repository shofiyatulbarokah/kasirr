<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$pesan = "";
$error = "";

if (isset($_POST['simpan'])) {

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


    // Validasi
    if ($nama_produk == "") {

        $error = "Nama produk wajib diisi.";

    } elseif ($harga == "" || $harga < 0) {

        $error = "Harga produk tidak valid.";

    } elseif ($stok == "" || $stok < 0) {

        $error = "Stok produk tidak valid.";

    } else {

        $query = mysqli_query(
            $koneksi,
            "INSERT INTO produk
            (nama_produk, harga, stok)
            VALUES
            ('$nama_produk', '$harga', '$stok')"
        );


        if ($query) {

            header("Location: produk.php");
            exit;

        } else {

            $error = "Data produk gagal ditambahkan: "
                   . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk | Kasir</title>

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



<!-- MAIN -->

<main class="main">


    <!-- TOPBAR -->

<header class="topbar">

        <div class="topbar-left">

            <h3>Tambah Produk</h3>

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


                <!-- NAMA PRODUK -->

                <div class="form-group">

                    <label for="nama_produk">
                        Nama Produk
                    </label>

                    <input
                        type="text"
                        id="nama_produk"
                        name="nama_produk"
                        placeholder="Contoh: nama produk"
                        value="<?= isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : ''; ?>"
                        required
                    >

                </div>



                <!-- HARGA -->

                <div class="form-group">

                    <label for="harga">
                        Harga
                    </label>

                    <div class="input-price">

                        <span>Rp</span>

                        <input
                            type="number"
                            id="harga"
                            name="harga"
                            placeholder="10000"
                            min="0"
                            step="0.01"
                            value="<?= isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : ''; ?>"
                            required
                        >

                    </div>

                </div>



                <!-- STOK -->

                <div class="form-group">

                    <label for="stok">
                        Stok
                    </label>

                    <input
                        type="number"
                        id="stok"
                        name="stok"
                        placeholder="0"
                        min="0"
                        value="<?= isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : ''; ?>"
                        required
                    >

                </div>



                <!-- BUTTON -->

                <div class="form-actions">

                    <a
                        href="produk.php"
                        class="btn-cancel"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        name="simpan"
                        class="btn-primary"
                    >
                        Simpan Produk
                    </button>

                </div>


            </form>

        </div>


    </section>

</main>


</body>
</html>