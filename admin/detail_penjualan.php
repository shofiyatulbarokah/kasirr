<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

/* Cek ID penjualan */
if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: penjualan.php");
    exit;
}

$id_penjualan = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);

/* Ambil data transaksi */
$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT
        p.id_penjualan,
        p.tanggal_penjualan,
        p.total_harga,
        pl.nama_pelanggan
     FROM penjualan p
     LEFT JOIN pelanggan pl
        ON p.id_pelanggan = pl.id_pelanggan
     WHERE p.id_penjualan='$id_penjualan'"
);

/* Jika transaksi tidak ditemukan */
if (mysqli_num_rows($query_penjualan) == 0) {
    header("Location: penjualan.php");
    exit;
}

$data_penjualan = mysqli_fetch_assoc($query_penjualan);

/* Ambil detail produk */
$query_detail = mysqli_query(
    $koneksi,
    "SELECT
        d.id_detail,
        d.jumlah_produk,
        d.subtotal,
        pr.nama_produk,
        pr.harga
     FROM detail_penjualan d
     JOIN produk pr
        ON d.id_produk = pr.id_produk
     WHERE d.id_penjualan='$id_penjualan'
     ORDER BY d.id_detail ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Penjualan</title>

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


<!-- MAIN -->
<main class="main">

    <!-- TOPBAR -->
    <header class="topbar">

        <div class="topbar-left">

            <h3>Detail Penjualan</h3>

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

        <div class="page-header">

            <div>
                <h1>Detail Penjualan</h1>

                <p>
                    Informasi lengkap transaksi
                    #<?= $data_penjualan['id_penjualan']; ?>
                </p>
            </div>

            <a href="penjualan.php" class="btn-primary">
                ← Kembali
            </a>

        </div>


        <!-- INFORMASI TRANSAKSI -->
        <div class="form-box">

            <h3>Informasi Transaksi</h3>

            <div class="form-group">
                <label>ID Penjualan</label>

                <input
                    type="text"
                    value="#<?= $data_penjualan['id_penjualan']; ?>"
                    readonly
                >
            </div>


            <div class="form-group">
                <label>Pelanggan</label>

                <input
                    type="text"
                    value="<?= !empty($data_penjualan['nama_pelanggan'])
                        ? htmlspecialchars($data_penjualan['nama_pelanggan'])
                        : 'Pelanggan Umum'; ?>"
                    readonly
                >
            </div>


            <div class="form-group">
                <label>Tanggal Penjualan</label>

                <input
                    type="text"
                    value="<?= date(
                        'd-m-Y H:i',
                        strtotime($data_penjualan['tanggal_penjualan'])
                    ); ?>"
                    readonly
                >
            </div>

        </div>


        <!-- DETAIL PRODUK -->
        <div class="table-box">

            <div style="padding: 20px 20px 0;">
                <h3>Produk yang Dibeli</h3>
            </div>

            <table>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                $no = 1;

                if (mysqli_num_rows($query_detail) > 0):

                    while ($detail = mysqli_fetch_assoc($query_detail)):
                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>

                        <td>
                            <strong>
                                <?= htmlspecialchars(
                                    $detail['nama_produk']
                                ); ?>
                            </strong>
                        </td>

                        <td>
                            Rp
                            <?= number_format(
                                $detail['harga'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>
                            <?= $detail['jumlah_produk']; ?>
                        </td>

                        <td>
                            <strong>
                                Rp
                                <?= number_format(
                                    $detail['subtotal'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </strong>
                        </td>

                    </tr>

                <?php
                    endwhile;

                else:
                ?>

                    <tr>
                        <td colspan="5" class="empty">
                            Belum ada detail produk.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

                <tfoot>

                    <tr>

                        <td
                            colspan="4"
                            style="text-align: right;"
                        >
                            <strong>Total Harga</strong>
                        </td>

                        <td>
                            <strong>
                                Rp
                                <?= number_format(
                                    $data_penjualan['total_harga'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </strong>
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </section>

</main>

</body>
</html>