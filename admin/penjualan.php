<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";


/* =========================
   HAPUS PENJUALAN
========================= */

if (isset($_GET['hapus'])) {

    $id_penjualan = mysqli_real_escape_string(
        $koneksi,
        $_GET['hapus']
    );

    $hapus = mysqli_query(
        $koneksi,
        "DELETE FROM penjualan
         WHERE id_penjualan='$id_penjualan'"
    );

    if ($hapus) {

        header("Location: penjualan.php");
        exit;

    }
}


/* =========================
   DATA PENJUALAN
========================= */

$cari = isset($_GET['cari'])
    ? trim($_GET['cari'])
    : '';

$cari_sql = mysqli_real_escape_string($koneksi, $cari);

$query = mysqli_query(
    $koneksi,
    "SELECT
        p.id_penjualan,
        p.id_pelanggan,
        p.tanggal_penjualan,
        p.total_harga,
        pl.nama_pelanggan
     FROM penjualan p
     LEFT JOIN pelanggan pl
        ON p.id_pelanggan = pl.id_pelanggan
     WHERE
        CAST(p.id_penjualan AS CHAR) LIKE '%$cari_sql%'
        OR pl.nama_pelanggan LIKE '%$cari_sql%'
        OR DATE_FORMAT(p.tanggal_penjualan, '%d-%m-%Y') LIKE '%$cari_sql%'
     ORDER BY p.id_penjualan DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Data Penjualan - Admin KASIR</title>

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

            <a href="pelanggan.php">
                <span class="menu-icon">♙</span>
                <span>Pelanggan</span>
            </a>

            <a href="penjualan.php" class="active">
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

            <h3>Penjualan</h3>

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


        <!-- JUDUL -->

        <div class="page-header">


            <div>

                <h1>
                    Data Penjualan
                </h1>

                <p>
                    Kelola transaksi penjualan pelanggan.
                </p>

            </div>


            <a
                href="tambah_penjualan.php"
                class="btn-primary"
            >
                + Tambah Penjualan
            </a>


        </div>

        <!-- =========================
             SEARCH PENJUALAN
        ========================= -->

        <form method="GET" class="customer-search">

            <div class="customer-search-box">

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari ID penjualan, pelanggan, atau tanggal..."
                    value="<?= htmlspecialchars($cari); ?>"
                >

                <button
                    type="submit"
                    class="customer-search-button"
                >
                    Cari
                </button>

                <?php if ($cari != ''): ?>

                    <a
                        href="penjualan.php"
                        class="customer-reset-button"
                    >
                        Reset
                    </a>

                <?php endif; ?>

            </div>

        </form>
        <br><br>



        <!-- TABLE -->

        <div class="table-box">


            <table>


                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            ID Penjualan
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Total Harga
                        </th>

                        <th>
                            Aksi
                        </th>

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



                        <!-- ID PENJUALAN -->

                        <td>

                            #<?= $data['id_penjualan']; ?>

                        </td>



                        <!-- PELANGGAN -->

                        <td>

                            <strong>

                                <?= !empty($data['nama_pelanggan'])
                                    ? htmlspecialchars($data['nama_pelanggan'])
                                    : 'Pelanggan Umum'; ?>

                            </strong>

                        </td>



                        <!-- TANGGAL -->

                        <td>

                            <?= date(
                                'd-m-Y H:i',
                                strtotime($data['tanggal_penjualan'])
                            ); ?>

                        </td>



                        <!-- TOTAL -->

                        <td>

                            <strong>

                                Rp <?= number_format(
                                    $data['total_harga'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </strong>

                        </td>



                        <!-- AKSI -->

                        <td>

                            <a
                                href="detail_penjualan.php?id=<?= $data['id_penjualan']; ?>"
                                class="action-edit"
                            >
                                Detail
                            </a>

                            <a
                                href="invoice.php?id=<?= $data['id_penjualan']; ?>"
                                class="action-invoice"
                                target="_blank"
                            >
                                Invoice
                            </a>

                            <a
                                href="penjualan.php?hapus=<?= $data['id_penjualan']; ?>"
                                class="action-delete"
                                onclick="return confirm('Yakin ingin menghapus transaksi ini?')"
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
                            Belum ada data penjualan.
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