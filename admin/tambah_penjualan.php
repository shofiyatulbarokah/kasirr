<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$error = "";


/* =========================
   SIMPAN PENJUALAN
========================= */

if (isset($_POST['simpan'])) {

    $id_pelanggan = $_POST['id_pelanggan'] ?? '';

    $produk = $_POST['produk'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];


    /* =========================
       VALIDASI
    ========================= */

    if (empty($produk)) {

    $error = "Minimal pilih satu produk.";

    } else {

        mysqli_begin_transaction($koneksi);

        try {

            $total_harga = 0;
            $detail_data = [];


            /* =========================
               CEK PRODUK & STOK
            ========================= */

            foreach ($produk as $i => $id_produk) {

                $id_produk = (int) $id_produk;
                $jumlah_produk = isset($jumlah[$i])
                    ? (int) $jumlah[$i]
                    : 0;


                if ($id_produk <= 0) {
                    throw new Exception(
                        "Produk tidak valid."
                    );
                }


                if ($jumlah_produk <= 0) {
                    throw new Exception(
                        "Jumlah produk harus lebih dari 0."
                    );
                }


                $query_produk = mysqli_query(
                    $koneksi,
                    "SELECT
                        id_produk,
                        nama_produk,
                        harga,
                        stok
                     FROM produk
                     WHERE id_produk='$id_produk'
                     FOR UPDATE"
                );


                if (mysqli_num_rows($query_produk) == 0) {

                    throw new Exception(
                        "Produk tidak ditemukan."
                    );
                }


                $data_produk = mysqli_fetch_assoc(
                    $query_produk
                );


                if ($jumlah_produk > $data_produk['stok']) {

                    throw new Exception(
                        "Stok produk \""
                        . $data_produk['nama_produk']
                        . "\" tidak mencukupi. Stok tersedia: "
                        . $data_produk['stok']
                    );
                }


                $subtotal =
                    $data_produk['harga']
                    * $jumlah_produk;


                $total_harga += $subtotal;


                $detail_data[] = [
                    'id_produk' => $id_produk,
                    'jumlah' => $jumlah_produk,
                    'subtotal' => $subtotal
                ];
            }


            /* =========================
               SIMPAN PENJUALAN
            ========================= */

            if ($id_pelanggan == '') {
                $nilai_pelanggan = "NULL";
            } else {
                $nilai_pelanggan = "'" . mysqli_real_escape_string($koneksi, $id_pelanggan) . "'";
            }

            $query_penjualan = mysqli_query(
                $koneksi,
                "INSERT INTO penjualan
                (
                    id_pelanggan,
                    tanggal_penjualan,
                    total_harga
                )
                VALUES
                (
                    $nilai_pelanggan,
                    NOW(),
                    '$total_harga'
                )"
            );


            if (!$query_penjualan) {

                throw new Exception(
                    "Data penjualan gagal disimpan."
                );
            }


            $id_penjualan = mysqli_insert_id(
                $koneksi
            );


            /* =========================
               SIMPAN DETAIL + UPDATE STOK
            ========================= */

            foreach ($detail_data as $detail) {

                $id_produk = $detail['id_produk'];
                $jumlah_produk = $detail['jumlah'];
                $subtotal = $detail['subtotal'];


                $query_detail = mysqli_query(
                    $koneksi,
                    "INSERT INTO detail_penjualan
                    (
                        id_penjualan,
                        id_produk,
                        jumlah_produk,
                        subtotal
                    )
                    VALUES
                    (
                        '$id_penjualan',
                        '$id_produk',
                        '$jumlah_produk',
                        '$subtotal'
                    )"
                );


                if (!$query_detail) {

                    throw new Exception(
                        "Detail penjualan gagal disimpan."
                    );
                }


                $query_stok = mysqli_query(
                    $koneksi,
                    "UPDATE produk
                     SET stok = stok - $jumlah_produk
                     WHERE id_produk='$id_produk'"
                );


                if (!$query_stok) {

                    throw new Exception(
                        "Stok produk gagal diperbarui."
                    );
                }
            }


            /* =========================
               SELESAI
            ========================= */

            mysqli_commit($koneksi);

            header(
                "Location: penjualan.php"
            );

            exit;


        } catch (Exception $e) {

            mysqli_rollback($koneksi);

            $error = $e->getMessage();
        }
    }
}


/* =========================
   DATA PELANGGAN
========================= */

$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT
        id_pelanggan,
        nama_pelanggan
     FROM pelanggan
     ORDER BY nama_pelanggan ASC"
);


/* =========================
   DATA PRODUK
========================= */

$query_produk = mysqli_query(
    $koneksi,
    "SELECT
        id_produk,
        nama_produk,
        harga,
        stok
     FROM produk
     WHERE stok > 0
     ORDER BY nama_produk ASC"
);

$produk_data = [];

while ($p = mysqli_fetch_assoc($query_produk)) {

    $produk_data[] = $p;
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

    <title>
        Tambah Penjualan - Admin KASIR
    </title>

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

            <h3>Tambah Penjualan</h3>

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


        <div class="form-header">

            <div>

                <h1>
                    Tambah Penjualan
                </h1>

                <p>
                    Pilih pelanggan dan produk yang dibeli.
                </p>

            </div>

        </div>



        <?php if ($error != ""): ?>

            <div class="alert-error">

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>



        <!-- FORM -->

        <div class="form-box">


            <form method="POST">


                <!-- PELANGGAN -->

                <div class="form-group">

                    <label for="id_pelanggan">
                        Pelanggan
                    </label>

                    <select
                        name="id_pelanggan"
                        id="id_pelanggan">

                        <option value="">
                            Pelanggan Umum
                        </option>


                        <?php while ($pelanggan = mysqli_fetch_assoc($query_pelanggan)): ?>

                            <option
                                value="<?= $pelanggan['id_pelanggan']; ?>"
                                <?= (
                                    isset($_POST['id_pelanggan'])
                                    && $_POST['id_pelanggan']
                                    == $pelanggan['id_pelanggan']
                                ) ? 'selected' : ''; ?>
                            >

                                <?= htmlspecialchars(
                                    $pelanggan['nama_pelanggan']
                                ); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>



                </div>



                <!-- PRODUK -->

                <div class="form-group">

                    <label>
                        Produk
                    </label>

                    <div id="produk-container">


                        <!-- BARIS PRODUK -->

                        <div class="produk-row">


                            <select
                                name="produk[]"
                                class="produk-select"
                                required
                            >

                                <option value="">
                                    -- Pilih Produk --
                                </option>


                                <?php foreach ($produk_data as $p): ?>

                                    <option
                                        value="<?= $p['id_produk']; ?>"
                                        data-harga="<?= $p['harga']; ?>"
                                        data-stok="<?= $p['stok']; ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $p['nama_produk']
                                        ); ?>

                                        -
                                        Rp <?= number_format(
                                            $p['harga'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>

                                        (Stok:
                                        <?= $p['stok']; ?>)

                                    </option>

                                <?php endforeach; ?>

                            </select>


                            <input
                                type="number"
                                name="jumlah[]"
                                class="jumlah-input"
                                min="1"
                                value="1"
                                placeholder="Jumlah"
                                required
                            >


                            <input
                                type="text"
                                class="subtotal-input"
                                value="Rp 0"
                                readonly
                            >


                            <button
                                type="button"
                                class="btn-remove"
                                onclick="hapusProduk(this)"
                            >
                                Hapus
                            </button>


                        </div>


                    </div>


                    <!-- TAMBAH PRODUK -->

                    <button
                        type="button"
                        class="btn-add-product"
                        onclick="tambahProduk()"
                    >
                        + Tambah Produk
                    </button>

                </div>



                <!-- TOTAL -->

                <div class="form-group">

                    <label>
                        Total Harga
                    </label>

                    <input
                        type="text"
                        id="total_harga"
                        value="Rp 0"
                        readonly
                    >

                </div>



                <!-- BUTTON -->

                <div class="form-action">


                    <a
                        href="penjualan.php"
                        class="btn-cancel"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        name="simpan"
                        class="btn-primary"
                    >
                        Simpan Penjualan
                    </button>


                </div>


            </form>


        </div>


    </section>


</div>



<!-- =========================
     JAVASCRIPT
========================= -->

<script>

const produkData = <?= json_encode($produk_data); ?>;


/* =========================
   FORMAT RUPIAH
========================= */

function formatRupiah(angka) {

    return 'Rp ' + Number(angka).toLocaleString(
        'id-ID'
    );

}


/* =========================
   UPDATE SUBTOTAL
========================= */

function updateTotal() {

    let total = 0;


    document
        .querySelectorAll('.produk-row')
        .forEach(function(row) {

            const select =
                row.querySelector('.produk-select');

            const jumlah =
                row.querySelector('.jumlah-input');

            const subtotalInput =
                row.querySelector('.subtotal-input');


            const option =
                select.options[select.selectedIndex];


            if (
                option
                && option.value
            ) {

                const harga =
                    Number(
                        option.dataset.harga
                    );

                const stok =
                    Number(
                        option.dataset.stok
                    );

                let jumlahValue =
                    Number(jumlah.value);


                if (jumlahValue > stok) {

                    jumlahValue = stok;

                    jumlah.value = stok;
                }


                const subtotal =
                    harga * jumlahValue;


                subtotalInput.value =
                    formatRupiah(subtotal);


                total += subtotal;

            } else {

                subtotalInput.value =
                    'Rp 0';
            }

        });


    document.getElementById(
        'total_harga'
    ).value =
        formatRupiah(total);

}


/* =========================
   EVENT PRODUK
========================= */

function pasangEvent(row) {

    const select =
        row.querySelector('.produk-select');

    const jumlah =
        row.querySelector('.jumlah-input');


    select.addEventListener(
        'change',
        updateTotal
    );


    jumlah.addEventListener(
        'input',
        updateTotal
    );

}


/* =========================
   TAMBAH PRODUK
========================= */

function tambahProduk() {

    const container =
        document.getElementById(
            'produk-container'
        );


    const row =
        document.createElement('div');


    row.className =
        'produk-row';


    let options =
        '<option value="">-- Pilih Produk --</option>';


    produkData.forEach(function(p) {

        options += `
            <option
                value="${p.id_produk}"
                data-harga="${p.harga}"
                data-stok="${p.stok}"
            >
                ${p.nama_produk}
                - Rp ${Number(p.harga).toLocaleString('id-ID')}
                (Stok: ${p.stok})
            </option>
        `;

    });


    row.innerHTML = `

        <select
            name="produk[]"
            class="produk-select"
            required
        >
            ${options}
        </select>


        <input
            type="number"
            name="jumlah[]"
            class="jumlah-input"
            min="1"
            value="1"
            placeholder="Jumlah"
            required
        >


        <input
            type="text"
            class="subtotal-input"
            value="Rp 0"
            readonly
        >


        <button
            type="button"
            class="btn-remove"
            onclick="hapusProduk(this)"
        >
            Hapus
        </button>

    `;


    container.appendChild(row);


    pasangEvent(row);

}


/* =========================
   HAPUS PRODUK
========================= */

function hapusProduk(button) {

    const rows =
        document.querySelectorAll(
            '.produk-row'
        );


    if (rows.length <= 1) {

        alert(
            'Minimal harus ada satu produk.'
        );

        return;
    }


    button
        .closest('.produk-row')
        .remove();


    updateTotal();

}


/* =========================
   EVENT AWAL
========================= */

document
    .querySelectorAll('.produk-row')
    .forEach(function(row) {

        pasangEvent(row);

    });


updateTotal();

</script>


</body>
</html>