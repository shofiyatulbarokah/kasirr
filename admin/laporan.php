<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";


/* =========================
   FILTER TANGGAL
========================= */

$tanggal_awal = isset($_GET['tanggal_awal']) && $_GET['tanggal_awal'] != ''
    ? $_GET['tanggal_awal']
    : date('Y-m-01');

$tanggal_akhir = isset($_GET['tanggal_akhir']) && $_GET['tanggal_akhir'] != ''
    ? $_GET['tanggal_akhir']
    : date('Y-m-d');


/* Jika tanggal terbalik */
if ($tanggal_awal > $tanggal_akhir) {
    $temp = $tanggal_awal;
    $tanggal_awal = $tanggal_akhir;
    $tanggal_akhir = $temp;
}


/* Amankan input */
$tanggal_awal_aman = mysqli_real_escape_string(
    $koneksi,
    $tanggal_awal
);

$tanggal_akhir_aman = mysqli_real_escape_string(
    $koneksi,
    $tanggal_akhir
);


/* =========================
   AMBIL DATA LAPORAN
========================= */

$query = mysqli_query($koneksi, "
    SELECT
        p.id_penjualan,
        p.tanggal_penjualan,
        p.total_harga,
        pl.nama_pelanggan
    FROM penjualan p
    LEFT JOIN pelanggan pl
        ON p.id_pelanggan = pl.id_pelanggan
    WHERE p.tanggal_penjualan >= '$tanggal_awal_aman 00:00:00'
    AND p.tanggal_penjualan <= '$tanggal_akhir_aman 23:59:59'
    ORDER BY p.tanggal_penjualan DESC
");


/* =========================
   SIMPAN DATA
========================= */

$data_laporan = [];
$total_laporan = 0;

while ($data = mysqli_fetch_assoc($query)) {

    $data_laporan[] = $data;

    $total_laporan += $data['total_harga'];
}

$jumlah_transaksi = count($data_laporan);


/* =========================
   FORMAT TANGGAL
========================= */

$format_tanggal_awal = date(
    'd F Y',
    strtotime($tanggal_awal)
);

$format_tanggal_akhir = date(
    'd F Y',
    strtotime($tanggal_akhir)
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

    <title>Laporan Penjualan - KASIR</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<div class="layout">

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

            <a href="penjualan.php">
                <span class="menu-icon">▤</span>
                <span>Penjualan</span>
            </a>

            <a href="laporan.php" class="active">
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
         MAIN CONTENT
    ========================== -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">

            <div class="topbar-left">

                <h3>
                    Laporan
                </h3>

                <span>
                    Sistem Pengelolaan Kasir
                </span>

            </div>


            <div class="topbar-right">

                <div class="admin-profile">

                    <div class="admin-avatar">

                        <?= strtoupper(
                            substr($_SESSION['nama'], 0, 1)
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
        ========================== -->

        <section class="content laporan-page">


            <!-- HEADER LAPORAN -->

            <div class="laporan-title">

                <div>

                    <div class="laporan-kicker">
                        LAPORAN TRANSAKSI
                    </div>

                    <h1>
                        Penjualan
                    </h1>

                    <p>
                        Rekap transaksi berdasarkan rentang tanggal.
                    </p>

                </div>


                <button
                    type="button"
                    class="laporan-print"
                    onclick="window.print()"
                >
                    Cetak Laporan
                </button>

            </div>



            <!-- =========================
                 FILTER
            ========================== -->

            <div class="laporan-filter-bar">

                <form method="GET">

                    <div class="filter-label">
                        Periode
                    </div>


                    <div class="filter-input-group">

                        <div class="filter-item">

                            <label for="tanggal_awal">
                                Dari
                            </label>

                            <input
                                type="date"
                                id="tanggal_awal"
                                name="tanggal_awal"
                                value="<?= htmlspecialchars(
                                    $tanggal_awal
                                ); ?>"
                                required
                            >

                        </div>


                        <div class="filter-item">

                            <label for="tanggal_akhir">
                                Sampai
                            </label>

                            <input
                                type="date"
                                id="tanggal_akhir"
                                name="tanggal_akhir"
                                value="<?= htmlspecialchars(
                                    $tanggal_akhir
                                ); ?>"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="filter-submit"
                        >
                            Tampilkan
                        </button>

                    </div>

                </form>

            </div>



            <!-- =========================
                 INFO PERIODE
            ========================== -->

            <div class="laporan-period">

                <div>

                    <span class="period-label">
                        Periode laporan
                    </span>

                    <strong>
                        <?= $format_tanggal_awal; ?>
                        —
                        <?= $format_tanggal_akhir; ?>
                    </strong>

                </div>


                <div class="period-count">

                    <strong>
                        <?= $jumlah_transaksi; ?>
                    </strong>

                    <span>
                        transaksi
                    </span>

                </div>

            </div>



            <!-- =========================
                 TABEL LAPORAN
            ========================== -->

            <div class="laporan-table-wrapper">

                <table class="laporan-table">

                    <thead>

                        <tr>

                            <th class="col-no">
                                No
                            </th>

                            <th>
                                ID Penjualan
                            </th>

                            <th>
                                Pelanggan
                            </th>

                            <th>
                                Tanggal Transaksi
                            </th>

                            <th class="col-total">
                                Total Harga
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($jumlah_transaksi > 0): ?>

                        <?php $no = 1; ?>

                        <?php foreach ($data_laporan as $data): ?>

                            <tr>

                                <td class="col-no">
                                    <?= str_pad(
                                        $no++,
                                        2,
                                        '0',
                                        STR_PAD_LEFT
                                    ); ?>
                                </td>


                                <td>

                                    <span class="transaction-id">

                                        #<?= htmlspecialchars(
                                            $data['id_penjualan']
                                        ); ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="customer-name">

                                        <?= htmlspecialchars(
                                            $data['nama_pelanggan']
                                            ?? 'Pelanggan Umum'
                                        ); ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="transaction-date">

                                        <?= date(
                                            'd-m-Y H:i',
                                            strtotime(
                                                $data['tanggal_penjualan']
                                            )
                                        ); ?>

                                    </span>

                                </td>


                                <td class="col-total">

                                    Rp
                                    <?= number_format(
                                        $data['total_harga'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <tr>

                            <td
                                colspan="5"
                                class="laporan-empty"
                            >

                                <div class="empty-icon">
                                    —
                                </div>

                                <strong>
                                    Tidak ada transaksi
                                </strong>

                                <span>
                                    Tidak ditemukan transaksi
                                    pada periode yang dipilih.
                                </span>

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>


                    <?php if ($jumlah_transaksi > 0): ?>

                    <tfoot>

                        <tr>

                            <td
                                colspan="4"
                                class="total-label"
                            >
                                Total Penjualan
                            </td>

                            <td class="total-value">

                                Rp
                                <?= number_format(
                                    $total_laporan,
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </td>

                        </tr>

                    </tfoot>

                    <?php endif; ?>

                </table>

            </div>



            <!-- CATATAN -->

            <?php if ($jumlah_transaksi > 0): ?>

            <div class="laporan-note">

                Menampilkan
                <strong><?= $jumlah_transaksi; ?></strong>
                transaksi dengan total penjualan
                <strong>
                    Rp
                    <?= number_format(
                        $total_laporan,
                        0,
                        ',',
                        '.'
                    ); ?>
                </strong>.

            </div>

            <?php endif; ?>


        </section>

    </main>

</div>


</body>

</html>