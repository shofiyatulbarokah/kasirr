<?php

session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

/* =========================
   HAPUS PRODUK
========================= */

if (isset($_GET['hapus'])) {

    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    mysqli_query(
        $koneksi,
        "DELETE FROM produk WHERE id_produk='$id'"
    );

    header("Location: produk.php");
    exit;
}


/* =========================
   AMBIL DATA PRODUK
========================= */

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';

$cari_aman = mysqli_real_escape_string($koneksi, $cari);

if ($cari != '') {

    $query = mysqli_query(
        $koneksi,
        "SELECT id_produk, nama_produk, harga, stok
         FROM produk
         WHERE id_produk LIKE '%$cari_aman%'
         OR nama_produk LIKE '%$cari_aman%'
         ORDER BY id_produk DESC"
    );

} else {

    $query = mysqli_query(
        $koneksi,
        "SELECT id_produk, nama_produk, harga, stok
         FROM produk
         ORDER BY id_produk DESC"
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Produk | Kasir</title>

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


<!-- =========================
     MAIN
========================= -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">

        <div class="topbar-left">

            <h3>Produk</h3>

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



    <!-- =========================
         CONTENT
    ========================= -->

    <section class="content">


        <!-- JUDUL -->

        <div class="page-header">

            <div>

                <h1>Data Produk</h1>

                <p>
                    Kelola nama produk, harga, dan stok barang.
                </p>

            </div>


            <a
                href="tambah_produk.php"
                class="btn-primary"
            >
                + Tambah Produk
            </a>

        </div>


        <!-- PENCARIAN PRODUK -->

        <form method="GET" class="search-area">

            <div class="search-box">

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari nama produk..."
                    value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>"
                >

                <button type="submit" class="search-button">
                    Cari
                </button>

                <?php if (isset($_GET['cari']) && $_GET['cari'] != ''): ?>
                    <a href="produk.php" class="search-button">
                        Reset
                    </a>
                <?php endif; ?>

            </div>

        </form>
        <br><br>


        <!-- =========================
             TABLE
        ========================= -->

        <div class="table-box">

            <table>

                <thead>

                    <tr>

                        <th>No</th>

                        <th>ID Produk</th>

                        <th>Nama Produk</th>

                        <th>Harga</th>

                        <th>Stok</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                if (mysqli_num_rows($query) > 0):

                    while ($data = mysqli_fetch_assoc($query)):

                ?>

                    <tr>

                        <!-- NO -->

                        <td>
                            <?= $no++; ?>
                        </td>


                        <!-- ID -->

                        <td>
                            #<?= $data['id_produk']; ?>
                        </td>


                        <!-- NAMA -->

                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $data['nama_produk']
                                ); ?>
                            </strong>

                        </td>


                        <!-- HARGA -->

                        <td>

                            Rp <?= number_format(
                                $data['harga'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>


                        <!-- STOK -->

                        <td>

                            <?php if ($data['stok'] <= 5): ?>

                                <span class="stock-low">
                                    <?= $data['stok']; ?>
                                </span>

                            <?php else: ?>

                                <span class="stock-normal">
                                    <?= $data['stok']; ?>
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- AKSI -->

                        <td>

                            <a
                                href="edit_produk.php?id=<?= $data['id_produk']; ?>"
                                class="action-edit"
                            >
                                Edit
                            </a>


                            <a
                                href="produk.php?hapus=<?= $data['id_produk']; ?>"
                                class="action-delete"
                                onclick="return confirm('Yakin ingin menghapus produk ini?')"
                            >
                                Hapus
                            </a>

                        </td>

                    </tr>


                <?php

                    endwhile;

                else:

                ?>

                    <tr>

                        <td
                            colspan="6"
                            class="empty"
                        >
                            Belum ada data produk.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>
</html>