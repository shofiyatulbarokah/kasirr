<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: user.php");
    exit;
}

$id_user = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE id_user='$id_user'"
);

if (mysqli_num_rows($query) == 0) {
    header("Location: user.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

$error = "";

if (isset($_POST['update'])) {

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = $_POST['password'];

    $role = mysqli_real_escape_string(
        $koneksi,
        $_POST['role']
    );


    if ($nama == "") {

        $error = "Nama pengguna wajib diisi.";

    } elseif ($username == "") {

        $error = "Username wajib diisi.";

    } elseif ($role != "admin" && $role != "kasir") {

        $error = "Role pengguna tidak valid.";

    } elseif ($password != "" && strlen($password) < 6) {

        $error = "Password baru minimal 6 karakter.";

    } else {

        // Cek username agar tidak sama dengan user lain
        $cek = mysqli_query(
            $koneksi,
            "SELECT id_user 
             FROM users 
             WHERE username='$username'
             AND id_user != '$id_user'"
        );

        if (mysqli_num_rows($cek) > 0) {

            $error = "Username sudah digunakan oleh pengguna lain.";

        } else {

            // Jika password diisi, password diperbarui
            if ($password != "") {

                $password_hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $update = mysqli_query(
                    $koneksi,
                    "UPDATE users SET
                        nama='$nama',
                        username='$username',
                        password='$password_hash',
                        role='$role'
                     WHERE id_user='$id_user'"
                );

            } else {

                // Jika password kosong, password lama tetap digunakan
                $update = mysqli_query(
                    $koneksi,
                    "UPDATE users SET
                        nama='$nama',
                        username='$username',
                        role='$role'
                     WHERE id_user='$id_user'"
                );
            }


            if ($update) {

                // Jika admin mengedit akun yang sedang login,
                // session ikut diperbarui
                if ($_SESSION['id_user'] == $id_user) {

                    $_SESSION['nama'] = $nama;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;

                }

                header("Location: user.php");
                exit;

            } else {

                $error = "Data pengguna gagal diperbarui: "
                       . mysqli_error($koneksi);

            }
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

    <title>Edit Pengguna - Admin KASIR</title>

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



<div class="main">


    <!-- =========================
         TOPBAR
    ========================= -->

    <header class="topbar">

        <div class="topbar-left">

            <h3>
                Edit Pengguna
            </h3>

            <span>
                Sistem Pengelolaan Kasir
            </span>

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

    <div class="content">


        <div class="form-box">


            <?php if ($error != "") : ?>

                <div class="login-error">

                    <?= htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>



            <form method="POST">


                <!-- ID USER -->

                <div class="form-group">

                    <label>
                        ID User
                    </label>

                    <input
                        type="text"
                        value="<?= htmlspecialchars(
                            $data['id_user']
                        ); ?>"
                        readonly
                    >

                </div>



                <!-- NAMA -->

                <div class="form-group">

                    <label>
                        Nama Pengguna
                    </label>

                    <input
                        type="text"
                        name="nama"
                        value="<?= htmlspecialchars(
                            $data['nama']
                        ); ?>"
                        required
                    >

                </div>



                <!-- USERNAME -->

                <div class="form-group">

                    <label>
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars(
                            $data['username']
                        ); ?>"
                        required
                    >

                </div>



                <!-- PASSWORD -->

                <div class="form-group">

                    <label>
                        Password Baru
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Kosongkan jika tidak ingin mengubah password"
                    >

                </div>



                <!-- ROLE -->

                <div class="form-group">

                    <label>
                        Role
                    </label>

                    <select name="role" required>

                        <option
                            value="admin"
                            <?= $data['role'] == 'admin'
                                ? 'selected'
                                : ''; ?>
                        >
                            Admin
                        </option>

                        <option
                            value="kasir"
                            <?= $data['role'] == 'kasir'
                                ? 'selected'
                                : ''; ?>
                        >
                            Kasir
                        </option>

                    </select>

                </div>



                <!-- BUTTON -->

                <div class="form-action">

                    <a
                        href="user.php"
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