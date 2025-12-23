<?php
include "config/koneksi.php";
include "layout/header.php";

if (isset($_POST['simpan'])) {
    mysqli_query($koneksi, "INSERT INTO kas_masuk VALUES(
        NULL,
        '$_POST[tanggal]',
        '$_POST[event]',
        '$_POST[keterangan]',
        '$_POST[jumlah]'
    )");
}
?>

<h3>Kas Masuk</h3>
<br>

<form method="post">
    Tanggal <br>
    <input type="date" name="tanggal" required><br><br>

    Event <br>
    <input type="text" name="event" required><br><br>

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
    <input type="number" name="jumlah" placeholder="Rp" required><br><br>

    <button name="simpan">Simpan</button>
</form>

<table>
<tr>
    <th>Tanggal</th>
    <th>Event</th>
    <th>Keterangan</th>
    <th>Kas Masuk</th>
    <th>Aksi</th>
</tr>

<?php
$data = mysqli_query($koneksi, "SELECT * FROM kas_masuk ORDER BY tanggal DESC");
while ($d = mysqli_fetch_array($data)) {
?>
<tr>
    <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
    <td><?= $d['event'] ?></td>
    <td><?= $d['keterangan'] ?></td>
    <td>Rp <?= number_format($d['jumlah'], 0, ',', ',') ?></td>
    <td>
        <a class="btn-edit" href="kas_masuk_edit.php?id=<?= $d['id'] ?>">Edit</a>
        <a class="btn-hapus" 
           href="kas_masuk_hapus.php?id=<?= $d['id'] ?>"
           onclick="return confirm('Yakin hapus data ini?')">
           Hapus
        </a>
    </td>
</tr>
<?php } ?>
</table>


<?php include "layout/footer.php"; ?>