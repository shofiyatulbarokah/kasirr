<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";


/* =========================
   CEK ID PENJUALAN
========================= */

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: penjualan.php");
    exit;
}

$id_penjualan = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);


/* =========================
   DATA PENJUALAN
========================= */

$query_penjualan = mysqli_query(
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
     WHERE p.id_penjualan='$id_penjualan'"
);


/* =========================
   CEK TRANSAKSI
========================= */

if (mysqli_num_rows($query_penjualan) == 0) {
    header("Location: penjualan.php");
    exit;
}

$data_penjualan = mysqli_fetch_assoc($query_penjualan);


/* =========================
   DATA DETAIL PRODUK
========================= */

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Invoice #<?= $data_penjualan['id_penjualan']; ?>
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body class="invoice-page">


<div class="invoice-wrapper">


    <!-- =========================
         INVOICE
    ========================= -->

    <div class="invoice">


        <!-- =========================
             HEADER TOKO
        ========================= -->

        <div class="store">

            <div class="store-logo">
                K
            </div>

            <h1>
                KASIR
            </h1>

            <p>
                Management System
            </p>

            <p>
                Struk Transaksi Penjualan
            </p>

        </div>



        <!-- =========================
             INFORMASI TRANSAKSI
        ========================= -->

        <div class="transaction-info">


            <div class="info-row">

                <span class="info-label">
                    No. Transaksi
                </span>

                <span class="info-value">
                    #<?= $data_penjualan['id_penjualan']; ?>
                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Tanggal
                </span>

                <span class="info-value">

                    <?= date(
                        'd-m-Y H:i',
                        strtotime(
                            $data_penjualan['tanggal_penjualan']
                        )
                    ); ?>

                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Pelanggan
                </span>

                <span class="info-value">

                    <?= !empty($data_penjualan['nama_pelanggan'])
                        ? htmlspecialchars(
                            $data_penjualan['nama_pelanggan']
                        )
                        : 'Pelanggan Umum'; ?>

                </span>

            </div>


        </div>



        <!-- =========================
             DAFTAR PRODUK
        ========================= -->

        <div class="items">


            <?php

            if (mysqli_num_rows($query_detail) > 0):

                while (
                    $detail = mysqli_fetch_assoc($query_detail)
                ):

            ?>


                <div class="item">


                    <!-- NAMA PRODUK -->

                    <div class="item-name">

                        <?= htmlspecialchars(
                            $detail['nama_produk']
                        ); ?>

                    </div>



                    <!-- HARGA DAN JUMLAH -->

                    <div class="item-detail">


                        <span>

                            <?= $detail['jumlah_produk']; ?>

                            x

                            Rp

                            <?= number_format(
                                $detail['harga'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </span>


                        <span>

                            Rp

                            <?= number_format(
                                $detail['subtotal'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </span>


                    </div>


                </div>


            <?php

                endwhile;

            else:

            ?>


                <div class="item">

                    <div class="item-name">

                        Tidak ada produk.

                    </div>

                </div>


            <?php endif; ?>


        </div>



        <!-- =========================
             TOTAL
        ========================= -->

        <div class="total">


            <div class="total-row">


                <span class="total-label">

                    TOTAL

                </span>


                <span class="total-value">

                    Rp

                    <?= number_format(
                        $data_penjualan['total_harga'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </span>


            </div>


        </div>



        <!-- =========================
             FOOTER
        ========================= -->

        <div class="invoice-footer">


            <p class="thank-you">

                Terima kasih atas kunjungan Anda.

            </p>


            <p>

                Barang yang sudah dibeli
                tidak dapat dikembalikan.

            </p>


        </div>


    </div>



    <!-- =========================
         TOMBOL
    ========================= -->

    <div class="print-area">


        <button
            type="button"
            class="btn-print"
            onclick="window.print()"
        >
             Cetak Struk
        </button>


        <a
            href="penjualan.php"
            class="btn-back"
        >
            Kembali
        </a>


    </div>


</div>


</body>

</html>