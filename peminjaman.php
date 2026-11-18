<?php
include 'koneksi.php';

// Proses Tambah Peminjaman
if (isset($_POST['simpan_peminjaman'])) {
    $id_buku = $_POST['id_buku'];
    $id_anggota = $_POST['id_anggota'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $status = $_POST['status'];

    // Cek stok buku terlebih dahulu
    $cek_buku = mysqli_query($koneksi, "SELECT * FROM tabel_buku WHERE id_buku = '$id_buku'");
    $data_buku = mysqli_fetch_assoc($cek_buku);

    if ($data_buku['stok'] > 0 || $status != 'Dipinjam') {
        // Simpan transaksi peminjaman
        $query = "INSERT INTO tabel_peminjaman (id_buku, id_anggota, tanggal_pinjam, status) VALUES ('$id_buku', '$id_anggota', '$tanggal_pinjam', '$status')";
        mysqli_query($koneksi, $query);

        // Jika statusnya 'Dipinjam', kurangi stok buku sebanyak 1
        if ($status == 'Dipinjam') {
            mysqli_query($koneksi, "UPDATE tabel_buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
        }
    }
    
    header("Location: peminjaman.php");
    exit;
}

// Proses Edit / Update Peminjaman
if (isset($_POST['update_peminjaman'])) {
    $id_peminjaman = $_POST['id_peminjaman'];
    $id_buku_baru = $_POST['id_buku'];
    $id_anggota = $_POST['id_anggota'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $status_baru = $_POST['status'];

    // Ambil data lama sebelum di-update untuk cek perubahan status & buku
    $q_lama = mysqli_query($koneksi, "SELECT * FROM tabel_peminjaman WHERE id_peminjaman = '$id_peminjaman'");
    $d_lama = mysqli_fetch_assoc($q_lama);
    
    $id_buku_lama = $d_lama['id_buku'];
    $status_lama = $d_lama['status'];

    // Logika penyesuaian stok otomatis saat status/buku diubah
    if ($status_lama == 'Dipinjam' && $status_baru == 'Dikembalikan') {
        // Buku dikembalikan -> Stok buku lama bertambah
        mysqli_query($koneksi, "UPDATE tabel_buku SET stok = stok + 1 WHERE id_buku = '$id_buku_lama'");
    } else if ($status_lama == 'Dikembalikan' && $status_baru == 'Dipinjam') {
        // Status diubah lagi jadi dipinjam -> Stok buku berkurang
        mysqli_query($koneksi, "UPDATE tabel_buku SET stok = stok - 1 WHERE id_buku = '$id_buku_lama'");
    }

    // Update data transaksi peminjaman di database
    $query_update = "UPDATE tabel_peminjaman SET id_buku = '$id_buku_baru', id_anggota = '$id_anggota', tanggal_pinjam = '$tanggal_pinjam', status = '$status_baru' WHERE id_peminjaman = '$id_peminjaman'";
    mysqli_query($koneksi, $query_update);

    header("Location: peminjaman.php");
    exit;
}

// Proses Hapus Peminjaman
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Ambil data peminjaman sebelum dihapus untuk mengembalikan stok jika statusnya dipinjam
    $q_peminjaman = mysqli_query($koneksi, "SELECT * FROM tabel_peminjaman WHERE id_peminjaman = '$id'");
    $d_peminjaman = mysqli_fetch_assoc($q_peminjaman);

    if ($d_peminjaman) {
        $id_buku = $d_peminjaman['id_buku'];
        $status = $d_peminjaman['status'];

        // Jika statusnya saat dihapus adalah 'Dipinjam', kembalikan stok bukunya (+1)
        if ($status == 'Dipinjam') {
            mysqli_query($koneksi, "UPDATE tabel_buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");
        }

        // Hapus data peminjaman
        mysqli_query($koneksi, "DELETE FROM tabel_peminjaman WHERE id_peminjaman = '$id'");
    }

    header("Location: peminjaman.php");
    exit;
}

// Ambil data untuk mode Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $q_edit = mysqli_query($koneksi, "SELECT * FROM tabel_peminjaman WHERE id_peminjaman = '$id_edit'");
    $edit_data = mysqli_fetch_assoc($q_edit);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Peminjaman - E-Perpus</title>
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
                    <a href="anggota.php" class="block py-2.5 px-4 rounded hover:bg-slate-800 text-slate-300">Data Anggota</a>
                    <a href="peminjaman.php" class="block py-2.5 px-4 rounded bg-emerald-600 text-white font-medium">Transaksi Peminjaman</a>
                </nav>
            </div>
            <div class="text-xs text-slate-500">Localhost v1.0</div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Manajemen Transaksi Peminjaman</h2>
            
            <!-- Form Tambah / Edit Peminjaman -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
                <h3 class="text-lg font-semibold text-slate-700 mb-4">
                    <?= $edit_data ? 'Edit Status / Transaksi Peminjaman' : 'Tambah Transaksi Peminjaman'; ?>
                </h3>
                <form method="POST" class="grid grid-cols-5 gap-4">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_peminjaman" value="<?= $edit_data['id_peminjaman']; ?>">
                    <?php endif; ?>

                    <!-- Dropdown Buku -->
                    <select name="id_buku" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500 bg-white">
                        <option value="">Pilih Buku (Stok)</option>
                        <?php
                        $buku = mysqli_query($koneksi, "SELECT * FROM tabel_buku");
                        while ($b = mysqli_fetch_assoc($buku)) {
                            $selected = ($edit_data && $edit_data['id_buku'] == $b['id_buku']) ? 'selected' : '';
                            echo "<option value='".$b['id_buku']."' $selected>".$b['nama_buku']." (Stok: ".$b['stok'].")</option>";
                        }
                        ?>
                    </select>

                    <!-- Dropdown Anggota -->
                    <select name="id_anggota" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500 bg-white">
                        <option value="">Pilih Anggota</option>
                        <?php
                        $anggota = mysqli_query($koneksi, "SELECT * FROM tabel_anggota");
                        while ($a = mysqli_fetch_assoc($anggota)) {
                            $nama_agt = isset($a['nama_lengkap']) ? $a['nama_lengkap'] : ($a['nama'] ?? 'Tanpa Nama');
                            $selected = ($edit_data && $edit_data['id_anggota'] == $a['id_anggota']) ? 'selected' : '';
                            echo "<option value='".$a['id_anggota']."' $selected>".$nama_agt."</option>";
                        }
                        ?>
                    </select>

                    <input type="date" name="tanggal_pinjam" value="<?= $edit_data['tanggal_pinjam'] ?? ''; ?>" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500">

                    <!-- Dropdown Status -->
                    <select name="status" required class="border border-slate-300 p-2.5 rounded-lg focus:outline-emerald-500 bg-white">
                        <option value="">Pilih Status</option>
                        <option value="Dipinjam" <?= ($edit_data && $edit_data['status'] == 'Dipinjam') ? 'selected' : ''; ?>>Dipinjam</option>
                        <option value="Dikembalikan" <?= ($edit_data && $edit_data['status'] == 'Dikembalikan') ? 'selected' : ''; ?>>Dikembalikan</option>
                    </select>
                    
                    <?php if ($edit_data): ?>
                        <div class="flex space-x-2">
                            <button type="submit" name="update_peminjaman" class="bg-amber-500 text-white font-medium rounded-lg px-3 py-2.5 hover:bg-amber-600 transition w-full text-sm">Update</button>
                            <a href="peminjaman.php" class="bg-slate-300 text-slate-700 font-medium rounded-lg px-3 py-2.5 hover:bg-slate-400 transition text-center text-sm">Batal</a>
                        </div>
                    <?php else: ?>
                        <button type="submit" name="simpan_peminjaman" class="bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition">Simpan Transaksi</button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabel Daftar Peminjaman -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 text-sm border-b border-slate-200">
                            <th class="p-4">No</th>
                            <th class="p-4">Nama Anggota</th>
                            <th class="p-4">Judul Buku</th>
                            <th class="p-4">Tanggal Pinjam</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700 text-sm">
                        <?php
                        $no = 1;
                        $sql = "SELECT tabel_peminjaman.*, tabel_buku.nama_buku 
                                FROM tabel_peminjaman 
                                JOIN tabel_buku ON tabel_peminjaman.id_buku = tabel_buku.id_buku";
                        $data = mysqli_query($koneksi, $sql);
                        
                        while ($row = mysqli_fetch_assoc($data)) {
                            $id_agt = $row['id_anggota'];
                            $q_agt = mysqli_query($koneksi, "SELECT * FROM tabel_anggota WHERE id_anggota = '$id_agt'");
                            $d_agt = mysqli_fetch_assoc($q_agt);
                            $nama_anggota_tampil = $d_agt['nama_lengkap'] ?? ($d_agt['nama'] ?? 'Tidak Diketahui');
                        ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-4"><?= $no++; ?></td>
                            <td class="p-4 font-medium"><?= $nama_anggota_tampil; ?></td>
                            <td class="p-4"><?= $row['nama_buku']; ?></td>
                            <td class="p-4"><?= $row['tanggal_pinjam']; ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $row['status'] == 'Dipinjam' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'; ?>">
                                    <?= $row['status']; ?>
                                </span>
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <a href="peminjaman.php?edit=<?= $row['id_peminjaman']; ?>" class="text-amber-600 hover:underline font-medium">Edit</a>
                                <a href="peminjaman.php?hapus=<?= $row['id_peminjaman']; ?>" onclick="return confirm('Yakin ingin menghapus transaksi ini? Stok buku akan dikembalikan.')" class="text-rose-600 hover:underline font-medium">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if(mysqli_num_rows($data) == 0) { ?>
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">Belum ada data transaksi peminjaman.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>