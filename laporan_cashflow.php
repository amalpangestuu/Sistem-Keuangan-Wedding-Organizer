<?php
include "config/koneksi.php";
include "layout/header.php";

// ========== FILTER PARAMETERS ==========
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_event = isset($_GET['event']) ? $_GET['event'] : '';

// Query condition builder
$where_conditions = [];
if ($filter_bulan) {
    $where_conditions[] = "DATE_FORMAT(tanggal, '%Y-%m') = '$filter_bulan'";
}
if ($filter_event) {
    $where_conditions[] = "event LIKE '%$filter_event%'";
}
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// ========== AMBIL DATA ==========
$masuk = mysqli_fetch_array(mysqli_query(
    $koneksi, "SELECT SUM(jumlah) AS total FROM kas_masuk $where_clause"
))['total'] ?? 0;

$keluar = mysqli_fetch_array(mysqli_query(
    $koneksi, "SELECT SUM(jumlah) AS total FROM kas_keluar $where_clause"
))['total'] ?? 0;

$saldo = $masuk - $keluar;

// ========== AMBIL LIST EVENT ==========
$event_list = mysqli_query($koneksi, "
    SELECT DISTINCT event FROM (
        SELECT event FROM kas_masuk
        UNION
        SELECT event FROM kas_keluar
    ) as events
    ORDER BY event ASC
");
?>

<h3>📊 Laporan Cashflow</h3>

<!-- ========== FILTER SECTION ========== -->
<div class="filter-container" style="margin-bottom: 20px;">
    <div class="filter-header">
        <h4>🔍 Filter Laporan</h4>
        <?php if ($filter_bulan || $filter_event): ?>
            <a href="laporan_cashflow.php" class="btn-reset-filter">🔄 Reset Filter</a>
        <?php endif; ?>
    </div>
    
    <form method="GET" action="" class="filter-form">
        <div class="filter-group">
            <label>📅 Pilih Bulan:</label>
            <input class="full" type="month" name="bulan" value="<?= $filter_bulan ?>">
        </div>
        
        <div class="filter-group">
            <label>🎉 Pilih Event:</label>
            <select class="full" name="event">
                <option value="">-- Semua Event --</option>
                <?php 
                mysqli_data_seek($event_list, 0);
                while ($evt = mysqli_fetch_array($event_list)): 
                ?>
                    <option value="<?= $evt['event'] ?>" 
                            <?= ($filter_event == $evt['event']) ? 'selected' : '' ?>>
                        <?= $evt['event'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="filter-actions">
            <br>
            <button type="submit" class="btn-filter">Terapkan Filter</button>
        </div>
    </form>
    
    <?php if ($filter_bulan || $filter_event): ?>
        <div class="filter-active-info">
            <strong>Filter Aktif:</strong>
            <?php if ($filter_bulan): ?>
                <span class="filter-badge">
                    📅 <?= date('F Y', strtotime($filter_bulan . '-01')) ?>
                </span>
            <?php endif; ?>
            <?php if ($filter_event): ?>
                <span class="filter-badge">
                    🎉 <?= $filter_event ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ========== RINGKASAN ========== -->
<table>
    <tr>
        <th>Total Kas Masuk</th>
        <th>Total Kas Keluar</th>
        <th>Saldo Akhir</th>
    </tr>
    <tr>
        <td style="background-color: #d4edda; font-weight: bold;">
            Rp <?= number_format($masuk, 0, ',', '.') ?>
        </td>
        <td style="background-color: #f8d7da; font-weight: bold;">
            Rp <?= number_format($keluar, 0, ',', '.') ?>
        </td>
        <td style="background-color: <?= $saldo >= 0 ? '#d4edda' : '#f8d7da' ?>; font-weight: bold;">
            Rp <?= number_format($saldo, 0, ',', '.') ?>
        </td>
    </tr>
</table>

<br>

<!-- ========== DETAIL KAS MASUK ========== -->
<h4 style="color: #155724; border-bottom: 3px solid #28a745; padding-bottom: 10px;">
    💰 Detail Kas Masuk
</h4>
<table>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Event</th>
        <th>Keterangan</th>
        <th>Jumlah</th>
    </tr>
    <?php
    $no = 1;
    $data_masuk = mysqli_query($koneksi, "SELECT * FROM kas_masuk $where_clause ORDER BY tanggal DESC");
    $has_data = false;
    while ($d = mysqli_fetch_array($data_masuk)) {
        $has_data = true;
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
        <td><strong><?= $d['event'] ?></strong></td>
        <td><?= $d['keterangan'] ?></td>
        <td style="color: #28a745; font-weight: bold;">
            Rp <?= number_format($d['jumlah'], 0, ',', '.') ?>
        </td>
    </tr>
    <?php } ?>
    
    <?php if (!$has_data): ?>
    <tr>
        <td colspan="5" style="text-align: center; padding: 20px; color: #7f8c8d;">
            Tidak ada data kas masuk
        </td>
    </tr>
    <?php endif; ?>
    
    <tr style="background-color: #28a745; color: white;">
        <th colspan="4" style="text-align: right; padding: 15px;">TOTAL KAS MASUK</th>
        <th style="padding: 15px;">Rp <?= number_format($masuk, 0, ',', '.') ?></th>
    </tr>
</table>

<br><br>

<!-- ========== DETAIL KAS KELUAR ========== -->
<h4 style="color: #721c24; border-bottom: 3px solid #dc3545; padding-bottom: 10px;">
    💸 Detail Kas Keluar
</h4>
<table>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Event</th>
        <th>Keterangan</th>
        <th>Jumlah</th>
    </tr>
    <?php
    $no = 1;
    $data_keluar = mysqli_query($koneksi, "SELECT * FROM kas_keluar $where_clause ORDER BY tanggal DESC");
    $has_data = false;
    while ($d = mysqli_fetch_array($data_keluar)) {
        $has_data = true;
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
        <td><strong><?= $d['event'] ?></strong></td>
        <td><?= $d['keterangan'] ?></td>
        <td style="color: #dc3545; font-weight: bold;">
            Rp <?= number_format($d['jumlah'], 0, ',', '.') ?>
        </td>
    </tr>
    <?php } ?>
    
    <?php if (!$has_data): ?>
    <tr>
        <td colspan="5" style="text-align: center; padding: 20px; color: #7f8c8d;">
            Tidak ada data kas keluar
        </td>
    </tr>
    <?php endif; ?>
    
    <tr style="background-color: #dc3545; color: white;">
        <th colspan="4" style="text-align: right; padding: 15px;">TOTAL KAS KELUAR</th>
        <th style="padding: 15px;">Rp <?= number_format($keluar, 0, ',', '.') ?></th>
    </tr>
</table>

<br><br>

<!-- ========== EXPORT BUTTONS ========== -->
<div style="display: flex; gap: 10px; margin-top: 20px;">
    <button onclick="exportPDF()" style="flex: 1;">
        📄 Export PDF
    </button>
    <button onclick="exportExcel()" style="flex: 1; background: linear-gradient(135deg, #28a745 0%, #218838 100%);">
        📊 Export EXCEL
    </button>
</div>

<script>
function exportPDF() {
    window.print();
}

function exportExcel() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = 'export_excel.php?' + params.toString();
}
</script>

<?php include "layout/footer.php"; ?>