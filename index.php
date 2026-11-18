<?php
include 'koneksi.php';

// Opsional: Cek status login (aktifkan jika halaman ini butuh login)
/*
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: login.php");
    exit;
}
*/
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Informasi Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-slate-900 text-white p-6 flex flex-col justify-between">
            <div>
                <h1 class="text-xl font-bold mb-8 tracking-wider text-emerald-400">E-PERPUS</h1>
                <nav class="space-y-3">
                    <a href="index.php" class="block py-2.5 px-4 rounded bg-emerald-600 text-white font-medium">Dashboard</a>
                    <a href="buku.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Data Buku</a>
                    <a href="anggota.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Data Anggota</a>
                    <a href="peminjaman.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Transaksi Peminjaman</a>
                </nav>
            </div>
            <div class="text-xs text-slate-500">Localhost v1.0</div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Dashboard</h2>
            
            <div class="grid grid-cols-3 gap-6">
                <!-- Total Buku -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <p class="text-slate-500 text-sm">Total Buku</p>
                    <h3 class="text-3xl font-extrabold text-slate-800 mt-2">
                        <?php 
                        $query_buku = mysqli_query($koneksi, "SELECT * FROM tabel_buku");
                        echo $query_buku ? mysqli_num_rows($query_buku) : 0;
                        ?>
                    </h3>
                </div>

                <!-- Total Anggota -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <p class="text-slate-500 text-sm">Total Anggota</p>
                    <h3 class="text-3xl font-extrabold text-slate-800 mt-2">
                        <?php 
                        $query_anggota = mysqli_query($koneksi, "SELECT * FROM tabel_anggota");
                        echo $query_anggota ? mysqli_num_rows($query_anggota) : 0;
                        ?>
                    </h3>
                </div>

                <!-- Total Peminjaman -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <p class="text-slate-500 text-sm">Total Peminjaman</p>
                    <h3 class="text-3xl font-extrabold text-slate-800 mt-2">
                        <?php 
                        $query_peminjaman = mysqli_query($koneksi, "SELECT * FROM tabel_peminjaman");
                        echo $query_peminjaman ? mysqli_num_rows($query_peminjaman) : 0;
                        ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</body>
</html>