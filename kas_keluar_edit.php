<?php
include "config/koneksi.php";
include "layout/header.php";

$id = $_GET['id'];
$data = mysqli_fetch_array(
    mysqli_query($koneksi, "SELECT * FROM kas_keluar WHERE id='$id'")
);

if (isset($_POST['update'])) {
    mysqli_query($koneksi, "UPDATE kas_keluar SET
        tanggal='$_POST[tanggal]',
        event='$_POST[event]',
        keterangan='$_POST[keterangan]',
        jumlah='$_POST[jumlah]'
        WHERE id='$id'
    ");
    header("location:kas_keluar.php");
}
?>

<h3>Edit Kas Keluar</h3><br>

<form method="post">
    Tanggal <br>
    <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required><br><br>

    Event <br>
    <input type="text" name="event" value="<?= $data['event'] ?>" required><br><br>

    Keterangan <br>
    <select name="keterangan" required>
        <option value="">-- Pilih Kategori --</option>
        <optgroup label="🍽️ Vendor Catering">
            <option value="Catering - DP" <?= $data['keterangan'] == 'Catering - DP' ? 'selected' : '' ?>>Catering - DP</option>
            <option value="Catering - Pelunasan" <?= $data['keterangan'] == 'Catering - Pelunasan' ? 'selected' : '' ?>>Catering - Pelunasan</option>
        </optgroup>
        <optgroup label="🎨 Vendor Dekorasi">
            <option value="Dekorasi - DP" <?= $data['keterangan'] == 'Dekorasi - DP' ? 'selected' : '' ?>>Dekorasi - DP</option>
            <option value="Dekorasi - Pelunasan" <?= $data['keterangan'] == 'Dekorasi - Pelunasan' ? 'selected' : '' ?>>Dekorasi - Pelunasan</option>
        </optgroup>
        <optgroup label="📸 Vendor Dokumentasi">
            <option value="Fotografi - DP" <?= $data['keterangan'] == 'Fotografi - DP' ? 'selected' : '' ?>>Fotografi - DP</option>
            <option value="Fotografi - Pelunasan" <?= $data['keterangan'] == 'Fotografi - Pelunasan' ? 'selected' : '' ?>>Fotografi - Pelunasan</option>
            <option value="Videografi - DP" <?= $data['keterangan'] == 'Videografi - DP' ? 'selected' : '' ?>>Videografi - DP</option>
            <option value="Videografi - Pelunasan" <?= $data['keterangan'] == 'Videografi - Pelunasan' ? 'selected' : '' ?>>Videografi - Pelunasan</option>
        </optgroup>
        <optgroup label="🎵 Vendor Hiburan">
            <option value="Entertainment - DP" <?= $data['keterangan'] == 'Entertainment - DP' ? 'selected' : '' ?>>Entertainment - DP</option>
            <option value="Entertainment - Pelunasan" <?= $data['keterangan'] == 'Entertainment - Pelunasan' ? 'selected' : '' ?>>Entertainment - Pelunasan</option>
            <option value="MC - DP" <?= $data['keterangan'] == 'MC - DP' ? 'selected' : '' ?>>MC - DP</option>
            <option value="MC - Pelunasan" <?= $data['keterangan'] == 'MC - Pelunasan' ? 'selected' : '' ?>>MC - Pelunasan</option>
        </optgroup>
        <optgroup label="👗 Vendor Busana">
            <option value="Makeup Artist - DP" <?= $data['keterangan'] == 'Makeup Artist - DP' ? 'selected' : '' ?>>Makeup Artist - DP</option>
            <option value="Makeup Artist - Pelunasan" <?= $data['keterangan'] == 'Makeup Artist - Pelunasan' ? 'selected' : '' ?>>Makeup Artist - Pelunasan</option>
            <option value="Wedding Dress - DP" <?= $data['keterangan'] == 'Wedding Dress - DP' ? 'selected' : '' ?>>Wedding Dress - DP</option>
            <option value="Wedding Dress - Pelunasan" <?= $data['keterangan'] == 'Wedding Dress - Pelunasan' ? 'selected' : '' ?>>Wedding Dress - Pelunasan</option>
        </optgroup>
        <optgroup label="🏢 Venue & Gedung">
            <option value="Sewa Gedung - DP" <?= $data['keterangan'] == 'Sewa Gedung - DP' ? 'selected' : '' ?>>Sewa Gedung - DP</option>
            <option value="Sewa Gedung - Pelunasan" <?= $data['keterangan'] == 'Sewa Gedung - Pelunasan' ? 'selected' : '' ?>>Sewa Gedung - Pelunasan</option>
        </optgroup>
        <optgroup label="🚗 Transport & Logistik">
            <option value="Transport Team" <?= $data['keterangan'] == 'Transport Team' ? 'selected' : '' ?>>Transport Team</option>
            <option value="Sewa Mobil Pengantin" <?= $data['keterangan'] == 'Sewa Mobil Pengantin' ? 'selected' : '' ?>>Sewa Mobil Pengantin</option>
            <option value="Logistik" <?= $data['keterangan'] == 'Logistik' ? 'selected' : '' ?>>Logistik</option>
        </optgroup>
        <optgroup label="💼 Operasional">
            <option value="Gaji Karyawan" <?= $data['keterangan'] == 'Gaji Karyawan' ? 'selected' : '' ?>>Gaji Karyawan</option>
            <option value="Operasional Kantor" <?= $data['keterangan'] == 'Operasional Kantor' ? 'selected' : '' ?>>Operasional Kantor</option>
            <option value="Listrik & Air" <?= $data['keterangan'] == 'Listrik & Air' ? 'selected' : '' ?>>Listrik & Air</option>
            <option value="Internet & Telepon" <?= $data['keterangan'] == 'Internet & Telepon' ? 'selected' : '' ?>>Internet & Telepon</option>
            <option value="Sewa Kantor" <?= $data['keterangan'] == 'Sewa Kantor' ? 'selected' : '' ?>>Sewa Kantor</option>
        </optgroup>
        <optgroup label="📢 Marketing & Promosi">
            <option value="Iklan Facebook/Instagram" <?= $data['keterangan'] == 'Iklan Facebook/Instagram' ? 'selected' : '' ?>>Iklan Facebook/Instagram</option>
            <option value="Iklan Google" <?= $data['keterangan'] == 'Iklan Google' ? 'selected' : '' ?>>Iklan Google</option>
            <option value="Brosur & Flyer" <?= $data['keterangan'] == 'Brosur & Flyer' ? 'selected' : '' ?>>Brosur & Flyer</option>
            <option value="Event Exhibition" <?= $data['keterangan'] == 'Event Exhibition' ? 'selected' : '' ?>>Event Exhibition</option>
        </optgroup>
        <optgroup label="📦 Perlengkapan">
            <option value="Souvenir" <?= $data['keterangan'] == 'Souvenir' ? 'selected' : '' ?>>Souvenir</option>
            <option value="Undangan" <?= $data['keterangan'] == 'Undangan' ? 'selected' : '' ?>>Undangan</option>
            <option value="Perlengkapan Acara" <?= $data['keterangan'] == 'Perlengkapan Acara' ? 'selected' : '' ?>>Perlengkapan Acara</option>
        </optgroup>
        <optgroup label="🔧 Lainnya">
            <option value="Maintenance Peralatan" <?= $data['keterangan'] == 'Maintenance Peralatan' ? 'selected' : '' ?>>Maintenance Peralatan</option>
            <option value="Konsumsi Team" <?= $data['keterangan'] == 'Konsumsi Team' ? 'selected' : '' ?>>Konsumsi Team</option>
            <option value="Lain-lain" <?= $data['keterangan'] == 'Lain-lain' ? 'selected' : '' ?>>Lain-lain</option>
        </optgroup>
    </select><br><br>

    Jumlah <br>
    <input type="number" name="jumlah" value="<?= $data['jumlah'] ?>" required><br><br>

    <button name="update">Update</button>
    <button type="button" onclick="window.location='kas_keluar.php'">Batal</button>
</form>

<?php include "layout/footer.php"; ?>