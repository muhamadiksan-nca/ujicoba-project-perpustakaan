<?php
include 'koneksi.php';

// Proses Tambah Buku
if (isset($_POST['simpan_buku'])) {
    $nama_buku = mysqli_real_escape_string($koneksi, $_POST['nama_buku']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $stok = (int)$_POST['stok'];

    $query = "INSERT INTO tabel_buku (nama_buku, penulis, stok) VALUES ('$nama_buku', '$penulis', '$stok')";
    mysqli_query($koneksi, $query);
    
    header("Location: buku.php");
    exit;
}

// Proses Edit Buku
if (isset($_POST['update_buku'])) {
    $id = $_POST['id_buku'];
    $nama_buku = mysqli_real_escape_string($koneksi, $_POST['nama_buku']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $stok = (int)$_POST['stok'];

    $query = "UPDATE tabel_buku SET nama_buku='$nama_buku', penulis='$penulis', stok='$stok' WHERE id_buku='$id'";
    mysqli_query($koneksi, $query);

    header("Location: buku.php");
    exit;
}

// Proses Hapus Buku (Aman dari Foreign Key Error)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus dulu data peminjaman yang terikat dengan buku ini agar tidak error foreign key
    mysqli_query($koneksi, "DELETE FROM tabel_peminjaman WHERE id_buku = '$id'");
    
    // Setelah itu baru hapus bukunya
    mysqli_query($koneksi, "DELETE FROM tabel_buku WHERE id_buku = '$id'");
    
    header("Location: buku.php");
    exit;
}

// Ambil data untuk diedit jika tombol Edit diklik
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $result_edit = mysqli_query($koneksi, "SELECT * FROM tabel_buku WHERE id_buku = '$id_edit'");
    $edit_data = mysqli_fetch_assoc($result_edit);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Buku - E-Perpus</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-slate-900 text-white p-6 flex flex-col justify-between">
            <div>
                <h1 class="text-xl font-bold mb-8 tracking-wider text-emerald-400">E-PERPUS</h1>
                <nav class="space-y-3">
                    <a href="index.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Dashboard</a>
                    <a href="buku.php" class="block py-2.5 px-4 rounded bg-emerald-600 text-white font-medium">Data Buku</a>
                    <a href="anggota.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Data Anggota</a>
                    <a href="peminjaman.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Transaksi Peminjaman</a>
                </nav>
            </div>
            <div class="text-xs text-slate-500">Localhost v1.0</div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Manajemen Data Buku</h2>
            
            <!-- Form Tambah / Edit Buku -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
                <h3 class="text-lg font-semibold text-slate-700 mb-4">
                    <?= $edit_data ? 'Edit Data Buku' : 'Tambah Buku Baru'; ?>
                </h3>
                <form method="POST" class="grid grid-cols-4 gap-4">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_buku" value="<?= $edit_data['id_buku']; ?>">
                    <?php endif; ?>

                    <input type="text" name="nama_buku" placeholder="Judul Buku" value="<?= $edit_data['nama_buku'] ?? ''; ?>" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500">
                    <input type="text" name="penulis" placeholder="Nama Penulis" value="<?= $edit_data['penulis'] ?? ''; ?>" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500">
                    <input type="number" name="stok" placeholder="Jumlah Stok" value="<?= $edit_data['stok'] ?? ''; ?>" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500">
                    
                    <?php if ($edit_data): ?>
                        <div class="flex space-x-2">
                            <button type="submit" name="update_buku" class="bg-amber-500 text-white font-medium rounded-lg px-4 py-2.5 hover:bg-amber-600 transition w-full">Update</button>
                            <a href="buku.php" class="bg-slate-300 text-slate-700 font-medium rounded-lg px-4 py-2.5 hover:bg-slate-400 transition text-center">Batal</a>
                        </div>
                    <?php else: ?>
                        <button type="submit" name="simpan_buku" class="bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition">Simpan Buku</button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabel Daftar Buku -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 text-sm border-b border-slate-200">
                            <th class="p-4">No</th>
                            <th class="p-4">Judul Buku</th>
                            <th class="p-4">Penulis</th>
                            <th class="p-4">Stok</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700 text-sm">
                        <?php
                        $no = 1;
                        $data = mysqli_query($koneksi, "SELECT * FROM tabel_buku");
                        while ($row = mysqli_fetch_assoc($data)) {
                        ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-4"><?= $no++; ?></td>
                            <td class="p-4 font-medium"><?= $row['nama_buku']; ?></td>
                            <td class="p-4"><?= $row['penulis']; ?></td>
                            <td class="p-4"><?= $row['stok']; ?></td>
                            <td class="p-4 text-center space-x-2">
                                <a href="buku.php?edit=<?= $row['id_buku']; ?>" class="text-amber-600 hover:underline font-medium">Edit</a>
                                <a href="buku.php?hapus=<?= $row['id_buku']; ?>" onclick="return confirm('Yakin ingin menghapus buku ini? Data transaksi terkait juga akan dibersihkan.')" class="text-rose-600 hover:underline font-medium">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if(mysqli_num_rows($data) == 0) { ?>
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada data buku.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>