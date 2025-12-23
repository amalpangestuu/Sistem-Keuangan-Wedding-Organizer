<?php
include "config/koneksi.php";
include "layout/header.php";

$id = $_GET['id'];
$data = mysqli_fetch_array(
    mysqli_query($koneksi, "SELECT * FROM kas_masuk WHERE id='$id'")
);

if (isset($_POST['update'])) {
    mysqli_query($koneksi, "UPDATE kas_masuk SET
        tanggal='$_POST[tanggal]',
        event='$_POST[event]',
        keterangan='$_POST[keterangan]',
        jumlah='$_POST[jumlah]'
        WHERE id='$id'
    ");
    header("location:kas_masuk.php");
}
?>

<h3>Edit Kas Masuk</h3><br>

<form method="post">
    Tanggal <br>
    <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required><br><br>

    Event <br>
    <input type="text" name="event" value="<?= $data['event'] ?>" required><br><br>

    Keterangan <br>
    <select name="keterangan" required>
        <option value="">-- Pilih Kategori --</option>
        <optgroup label="🍽️ Vendor Catering">
            <option value="Catering - DP">Catering - DP</option>
            <option value="Catering - Lunas">Catering - Lunas</option>
        </optgroup>
        <optgroup label="🎨 Vendor Dekorasi">
            <option value="Dekorasi - DP">Dekorasi - DP</option>
            <option value="Dekorasi - Lunas">Dekorasi - Lunas</option>
        </optgroup>
        <optgroup label="📸 Vendor Dokumentasi">
            <option value="Fotografi - DP">Fotografi - DP</option>
            <option value="Fotografi - Lunas">Fotografi - Lunas</option>
            <option value="Videografi - DP">Videografi - DP</option>
            <option value="Videografi - Lunas">Videografi - Lunas</option>
        </optgroup>
        <optgroup label="🎵 Vendor Hiburan">
            <option value="Entertainment - DP">Entertainment - DP</option>
            <option value="Entertainment - Lunas">Entertainment - Lunas</option>
            <option value="MC - DP">MC - DP</option>
            <option value="MC - Lunas">MC - Lunas</option>
        </optgroup>
        <optgroup label="👗 Vendor Busana">
            <option value="Makeup Artist - DP">Makeup Artist - DP</option>
            <option value="Makeup Artist - Lunas">Makeup Artist - Lunas</option>
            <option value="Wedding Dress - DP">Wedding Dress - DP</option>
            <option value="Wedding Dress - Lunas">Wedding Dress - Lunas</option>
        </optgroup>
        <optgroup label="🏢 Venue & Gedung">
            <option value="Sewa Gedung - DP">Sewa Gedung - DP</option>
            <option value="Sewa Gedung - Lunas">Sewa Gedung - Lunas</option>
        </optgroup>
        <optgroup label="🚗 Transport & Logistik">
            <option value="Transport Team">Transport Team</option>
            <option value="Sewa Mobil Pengantin">Sewa Mobil Pengantin</option>
            <option value="Logistik">Logistik</option>
        </optgroup>
        <optgroup label="💼 Operasional">
            <option value="Gaji Karyawan">Gaji Karyawan</option>
            <option value="Operasional Kantor">Operasional Kantor</option>
            <option value="Listrik & Air">Listrik & Air</option>
            <option value="Internet & Telepon">Internet & Telepon</option>
            <option value="Sewa Kantor">Sewa Kantor</option>
        </optgroup>
        <optgroup label="📢 Marketing & Promosi">
            <option value="Iklan Facebook/Instagram">Iklan Facebook/Instagram</option>
            <option value="Iklan Google">Iklan Google</option>
            <option value="Brosur & Flyer">Brosur & Flyer</option>
            <option value="Event Exhibition">Event Exhibition</option>
        </optgroup>
        <optgroup label="📦 Perlengkapan">
            <option value="Souvenir">Souvenir</option>
            <option value="Undangan">Undangan</option>
            <option value="Perlengkapan Acara">Perlengkapan Acara</option>
        </optgroup>
        <optgroup label="🔧 Lainnya">
            <option value="Maintenance Peralatan">Maintenance Peralatan</option>
            <option value="Konsumsi Team">Konsumsi Team</option>
            <option value="Lain-lain">Lain-lain</option>
        </optgroup>
    </select><br><br>

    Jumlah <br>
    <input type="number" name="jumlah" value="<?= $data['jumlah'] ?>" required><br><br>

    <button name="update">Update</button>
    <button type="button" onclick="window.location='kas_masuk.php'">Batal</button>
</form>

<?php include "layout/footer.php"; ?>