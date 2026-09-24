<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: pelanggan.php");
    exit;
}

$id_pelanggan = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan
     WHERE id_pelanggan='$id_pelanggan'"
);

if (mysqli_num_rows($query) == 0) {
    header("Location: pelanggan.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

$error = "";

if (isset($_POST['update'])) {

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


    if ($nama_pelanggan == "") {

        $error = "Nama pelanggan wajib diisi.";

    } else {

        $update = mysqli_query(
            $koneksi,
            "UPDATE pelanggan SET
                nama_pelanggan='$nama_pelanggan',
                alamat='$alamat',
                no_hp='$no_hp'
             WHERE id_pelanggan='$id_pelanggan'"
        );


        if ($update) {

            header("Location: pelanggan.php");
            exit;

        } else {

            $error = "Data pelanggan gagal diperbarui: "
                   . mysqli_error($koneksi);

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

    <title>Edit Pelanggan - Admin KASIR</title>

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



<!-- =========================
     MAIN
========================= -->

<div class="main">


    <!-- TOPBAR -->

    <header class="topbar">

        <div class="topbar-left">

            <h3>Edit Pelanggan</h3>

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

    <div class="content">


        <div class="form-box">


            <?php if ($error != "") : ?>

                <div class="login-error">

                    <?= htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>



            <form method="POST">


                <!-- ID PELANGGAN -->

                <div class="form-group">

                    <label>
                        ID Pelanggan
                    </label>

                    <input
                        type="text"
                        value="<?= htmlspecialchars($data['id_pelanggan']); ?>"
                        readonly
                    >

                </div>



                <!-- NAMA PELANGGAN -->

                <div class="form-group">

                    <label>
                        Nama Pelanggan
                    </label>

                    <input
                        type="text"
                        name="nama_pelanggan"
                        value="<?= htmlspecialchars($data['nama_pelanggan']); ?>"
                        placeholder="Masukkan nama pelanggan"
                        required
                    >

                </div>



                <!-- ALAMAT -->

                <div class="form-group">

                    <label>
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        rows="4"
                        placeholder="Masukkan alamat pelanggan"
                    ><?= htmlspecialchars($data['alamat'] ?? ''); ?></textarea>

                </div>



                <!-- NO HP -->

                <div class="form-group">

                    <label>
                        No. HP
                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        value="<?= htmlspecialchars($data['no_hp'] ?? ''); ?>"
                        placeholder="Contoh: 081234567890"
                        maxlength="20"
                    >

                </div>



                <!-- BUTTON -->

                <div class="form-action">


                    <a
                        href="pelanggan.php"
                        class="btn-cancel"
                    >
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