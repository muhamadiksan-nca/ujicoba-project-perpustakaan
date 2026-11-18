<?php
include 'koneksi.php';

// Proses Tambah Anggota
if (isset($_POST['simpan_anggota'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    $query = "INSERT INTO tabel_anggota (nama_lengkap, email, telepon) VALUES ('$nama_lengkap', '$email', '$telepon')";
    mysqli_query($koneksi, $query);
    
    header("Location: anggota.php");
    exit;
}

// Proses Edit Anggota
if (isset($_POST['update_anggota'])) {
    $id = $_POST['id_anggota'];
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    $query = "UPDATE tabel_anggota SET nama_lengkap='$nama_lengkap', email='$email', telepon='$telepon' WHERE id_anggota='$id'";
    mysqli_query($koneksi, $query);

    header("Location: anggota.php");
    exit;
}

// Proses Hapus Anggota
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM tabel_anggota WHERE id_anggota = '$id'");
    header("Location: anggota.php");
    exit;
}

// Ambil data untuk diedit jika tombol Edit diklik
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $result_edit = mysqli_query($koneksi, "SELECT * FROM tabel_anggota WHERE id_anggota = '$id_edit'");
    $edit_data = mysqli_fetch_assoc($result_edit);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Anggota - E-Perpus</title>
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
                    <a href="buku.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Data Buku</a>
                    <a href="anggota.php" class="block py-2.5 px-4 rounded bg-emerald-600 text-white font-medium">Data Anggota</a>
                    <a href="peminjaman.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Transaksi Peminjaman</a>
                </nav>
            </div>
            <div class="text-xs text-slate-500">Localhost v1.0</div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Kelola Data Anggota</h2>
            
            <!-- Form Tambah / Edit Anggota -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
                <h3 class="text-lg font-semibold text-slate-700 mb-4">
                    <?= $edit_data ? 'Edit Data Anggota' : 'Tambah Anggota Baru'; ?>
                </h3>
                <form method="POST" class="grid grid-cols-4 gap-4">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_anggota" value="<?= $edit_data['id_anggota']; ?>">
                    <?php endif; ?>

                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="<?= $edit_data['nama_lengkap'] ?? ''; ?>" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500">
                    <input type="email" name="email" placeholder="Email" value="<?= $edit_data['email'] ?? ''; ?>" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500">
                    <input type="text" name="telepon" placeholder="Nomor Telepon" value="<?= $edit_data['telepon'] ?? ''; ?>" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500">
                    
                    <?php if ($edit_data): ?>
                        <div class="flex space-x-2">
                            <button type="submit" name="update_anggota" class="bg-amber-500 text-white font-medium rounded-lg px-4 py-2.5 hover:bg-amber-600 transition w-full">Update</button>
                            <a href="anggota.php" class="bg-slate-300 text-slate-700 font-medium rounded-lg px-4 py-2.5 hover:bg-slate-400 transition text-center">Batal</a>
                        </div>
                    <?php else: ?>
                        <button type="submit" name="simpan_anggota" class="bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition">Simpan Anggota</button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabel Daftar Anggota -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 text-sm border-b border-slate-200">
                            <th class="p-4">No</th>
                            <th class="p-4">Nama Anggota</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Telepon</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700 text-sm">
                        <?php
                        $no = 1;
                        $data = mysqli_query($koneksi, "SELECT * FROM tabel_anggota");
                        while ($row = mysqli_fetch_assoc($data)) {
                        ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-4"><?= $no++; ?></td>
                            <td class="p-4 font-medium"><?= $row['nama_lengkap']; ?></td>
                            <td class="p-4"><?= $row['email']; ?></td>
                            <td class="p-4"><?= $row['telepon']; ?></td>
                            <td class="p-4 text-center space-x-2">
                                <a href="anggota.php?edit=<?= $row['id_anggota']; ?>" class="text-amber-600 hover:underline font-medium">Edit</a>
                                <a href="anggota.php?hapus=<?= $row['id_anggota']; ?>" onclick="return confirm('Yakin ingin menghapus anggota ini?')" class="text-rose-600 hover:underline font-medium">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if(mysqli_num_rows($data) == 0) { ?>
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada data anggota.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>