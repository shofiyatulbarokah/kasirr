<?php

session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

/* =========================
   HAPUS USER
========================= */

if (isset($_GET['hapus'])) {

    $id_user = mysqli_real_escape_string(
        $koneksi,
        $_GET['hapus']
    );

    /* Jangan hapus akun yang sedang digunakan */
    if ($id_user == $_SESSION['id_user']) {

        header("Location: user.php?error=self");
        exit;

    }

    mysqli_query(
        $koneksi,
        "DELETE FROM users
         WHERE id_user='$id_user'"
    );

    header("Location: user.php");
    exit;
}


/* =========================
   SEARCH USER
========================= */

$cari = isset($_GET['cari'])
    ? trim($_GET['cari'])
    : '';

$cari_sql = mysqli_real_escape_string(
    $koneksi,
    $cari
);


/* =========================
   AMBIL DATA USER
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT
        id_user,
        nama,
        username,
        role
     FROM users
     WHERE
        CAST(id_user AS CHAR) LIKE '%$cari_sql%'
        OR nama LIKE '%$cari_sql%'
        OR username LIKE '%$cari_sql%'
        OR role LIKE '%$cari_sql%'
     ORDER BY id_user DESC"
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

    <title>Pengguna | Admin KASIR</title>

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

            <a href="penjualan.php">
                <span class="menu-icon">▤</span>
                <span>Penjualan</span>
            </a>

            <a href="laporan.php">
                <span class="menu-icon">◷</span>
                <span>Laporan</span>
            </a>

            <a href="user.php" class="active">
                <span class="menu-icon">♙</span>
                <span>Users</span>
            </a>
            
        </nav>

    </aside>


<!-- =========================
     MAIN
========================= -->

<div class="main">


    <!-- =========================
         TOPBAR
    ========================= -->

    <header class="topbar">

        <div class="topbar-left">

            <h3>Users</h3>

            <span>
                Kelola akun Admin dan Kasir.
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
    ========================= -->

    <section class="content">


        <!-- PAGE HEADER -->

        <div class="page-header">

            <div>

                <h1>Data Pengguna</h1>

                <p>
                    Kelola akun pengguna yang dapat
                    mengakses sistem kasir.
                </p>

            </div>


            <a
                href="tambah_user.php"
                class="btn-primary"
            >
                + Tambah Pengguna
            </a>

        </div>


        <!-- ERROR -->

        <?php if (isset($_GET['error']) && $_GET['error'] == 'self'): ?>

            <div class="login-error">
                Akun yang sedang digunakan tidak dapat dihapus.
            </div>

        <?php endif; ?>

        <!-- =========================
             SEARCH USER
        ========================= -->

        <form method="GET" class="customer-search">

            <div class="customer-search-box">

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari ID user, nama, username, atau role..."
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
                        href="user.php"
                        class="customer-reset-button"
                    >
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

                        <th>ID User</th>

                        <th>Nama</th>

                        <th>Username</th>

                        <th>Role</th>

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
                            #<?= $data['id_user']; ?>
                        </td>


                        <!-- NAMA -->

                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $data['nama']
                                ); ?>
                            </strong>

                        </td>


                        <!-- USERNAME -->

                        <td>

                            @<?= htmlspecialchars(
                                $data['username']
                            ); ?>

                        </td>


                        <!-- ROLE -->

                        <td>

                            <?php if ($data['role'] == 'admin'): ?>

                                <span class="role-admin">
                                    Admin
                                </span>

                            <?php else: ?>

                                <span class="role-kasir">
                                    Kasir
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- AKSI -->

                        <td>

                            <a
                                href="edit_user.php?id=<?= $data['id_user']; ?>"
                                class="action-edit"
                            >
                                Edit
                            </a>


                            <?php if ($data['id_user'] != $_SESSION['id_user']): ?>

                                <a
                                    href="user.php?hapus=<?= $data['id_user']; ?>"
                                    class="action-delete"
                                    onclick="return confirm('Yakin ingin menghapus pengguna ini?')"
                                >
                                    Hapus
                                </a>

                            <?php endif; ?>

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
                            Belum ada data pengguna.
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