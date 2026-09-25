<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

/* =========================
   HAPUS PELANGGAN
========================= */

if (isset($_GET['hapus'])) {

    $id_pelanggan = mysqli_real_escape_string(
        $koneksi,
        $_GET['hapus']
    );

    $hapus = mysqli_query(
        $koneksi,
        "DELETE FROM pelanggan
         WHERE id_pelanggan='$id_pelanggan'"
    );

    if ($hapus) {
        header("Location: pelanggan.php");
        exit;
    }
}


/* =========================
   AMBIL DATA PELANGGAN
========================= */

$cari = isset($_GET['cari'])
    ? trim($_GET['cari'])
    : '';

$cari_sql = mysqli_real_escape_string($koneksi, $cari);

$query = mysqli_query(
    $koneksi,
    "SELECT
        id_pelanggan,
        nama_pelanggan,
        alamat,
        no_hp
     FROM pelanggan
     WHERE nama_pelanggan LIKE '%$cari_sql%'
        OR alamat LIKE '%$cari_sql%'
        OR no_hp LIKE '%$cari_sql%'
     ORDER BY id_pelanggan DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Pelanggan - Admin KASIR</title>

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

    <div class="main">

        <!-- TOPBAR -->

    <header class="topbar">

            <div class="topbar-left">

                <h3>Pelanggan</h3>

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


    <section class="content">


    <!-- JUDUL -->

    <div class="page-header">

        <div>

            <h1>Data Pelanggan</h1>

            <p>
                Kelola data nama, alamat, dan nomor HP pelanggan.
            </p>

        </div>


        <a
            href="tambah_pelanggan.php"
            class="btn-primary"
        >
            + Tambah Pelanggan
        </a>

    </div>

    <!-- =========================
     SEARCH PELANGGAN
    ========================= -->

    <form method="GET" class="customer-search">

        <div class="customer-search-box">

            <input
                type="text"
                name="cari"
                placeholder="Cari nama, alamat, atau nomor HP..."
                value="<?= htmlspecialchars($cari); ?>"
            >

            <button type="submit" class="customer-search-button">
                Cari
            </button>

            <?php if ($cari != ''): ?>

                <a href="pelanggan.php" class="customer-reset-button">
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

                    <th>ID Pelanggan</th>

                    <th>Nama Pelanggan</th>

                    <th>Alamat</th>

                    <th>No. HP</th>

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


                    <!-- ID PELANGGAN -->

                    <td>
                        #<?= $data['id_pelanggan']; ?>
                    </td>


                    <!-- NAMA PELANGGAN -->

                    <td>

                        <strong>
                            <?= htmlspecialchars(
                                $data['nama_pelanggan']
                            ); ?>
                        </strong>

                    </td>


                    <!-- ALAMAT -->

                    <td>

                        <?= !empty($data['alamat'])
                            ? htmlspecialchars($data['alamat'])
                            : '-'; ?>

                    </td>


                    <!-- NO HP -->

                    <td>

                        <?= !empty($data['no_hp'])
                            ? htmlspecialchars($data['no_hp'])
                            : '-'; ?>

                    </td>


                    <!-- AKSI -->

                    <td>

                        <a
                            href="edit_pelanggan.php?id=<?= $data['id_pelanggan']; ?>"
                            class="action-edit"
                        >
                            Edit
                        </a>


                        <a
                            href="pelanggan.php?hapus=<?= $data['id_pelanggan']; ?>"
                            class="action-delete"
                            onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"
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
                        Belum ada data pelanggan.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>
</div>

</body>
</html>