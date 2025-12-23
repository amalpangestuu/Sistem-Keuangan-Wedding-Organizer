<?php
include "config/koneksi.php";

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

// Filename with filter info
$filename = "Laporan_Cashflow_Per_Event";
if ($filter_bulan) {
    $filename .= "_" . date('M-Y', strtotime($filter_bulan . '-01'));
}
if ($filter_event) {
    $filename .= "_" . preg_replace('/[^A-Za-z0-9\-]/', '', $filter_event);
}
$filename .= "_" . date('Y-m-d_His') . ".xls";

// Set header untuk download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

// ========== GET SUMMARY DATA ==========
$masuk = mysqli_fetch_array(mysqli_query(
    $koneksi, "SELECT SUM(jumlah) AS total FROM kas_masuk $where_clause"
))['total'] ?? 0;

$keluar = mysqli_fetch_array(mysqli_query(
    $koneksi, "SELECT SUM(jumlah) AS total FROM kas_keluar $where_clause"
))['total'] ?? 0;

$saldo = $masuk - $keluar;

// ========== GET ALL EVENTS ==========
$query_events = "
    SELECT DISTINCT event FROM (
        SELECT event FROM kas_masuk $where_clause
        UNION
        SELECT event FROM kas_keluar $where_clause
    ) as events
    ORDER BY event ASC
";
$events_list = mysqli_query($koneksi, $query_events);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Cashflow Per Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .filter-info {
            text-align: center;
            font-size: 13px;
            margin-bottom: 20px;
            color: #0066cc;
            font-weight: bold;
        }
        .event-header {
            background-color: #1e40af;
            color: white;
            font-weight: bold;
            font-size: 16px;
            padding: 12px;
        }
        .header-green {
            background-color: #10b981;
            color: white;
            font-weight: bold;
        }
        .header-red {
            background-color: #ef4444;
            color: white;
            font-weight: bold;
        }
        .header-blue {
            background-color: #3b82f6;
            color: white;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
            font-weight: bold;
        }
        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .summary-row {
            background-color: #dbeafe;
            font-weight: bold;
        }
        .profit-positive {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: bold;
        }
        .profit-negative {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #1f2937;
        }
    </style>
</head>
<body>

<div class="title">📊 LAPORAN CASHFLOW PER EVENT</div>
<div class="subtitle">Wedding Organizer</div>

<?php if ($filter_bulan || $filter_event): ?>
<div class="filter-info">
    Filter: 
    <?php if ($filter_bulan): ?>
        Bulan <?= date('F Y', strtotime($filter_bulan . '-01')) ?>
    <?php endif; ?>
    <?php if ($filter_event): ?>
        | Event: <?= $filter_event ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="subtitle">Tanggal Export: <?= date('d-m-Y H:i:s') ?></div>
<br>

<!-- =========  RINGKASAN KESELURUHAN ============ -->
<table>
    <tr>
        <th colspan="3" style="background-color: #fff833ff; font-size: 16px;">RINGKASAN KESELURUHAN</th>
    </tr>
    <tr>
        <th>Total Kas Masuk</th>
        <th>Total Kas Keluar</th>
        <th>Saldo Akhir</th>
    </tr>
    <tr>
        <td style="background-color: #d1fae5;">Rp <?= number_format($masuk, 0, ',', '.') ?></td>
        <td style="background-color: #fee2e2;">Rp <?= number_format($keluar, 0, ',', '.') ?></td>
        <td style="background-color: <?= $saldo >= 0 ? '#d1fae5' : '#fee2e2' ?>;">
            Rp <?= number_format($saldo, 0, ',', '.') ?>
        </td>
    </tr>
</table>

<br><br>

<!-- ========== LAPORAN PER EVENT ============ -->

<?php
$event_number = 1;
while ($event_data = mysqli_fetch_array($events_list)):
    $event_name = $event_data['event'];
    
    // Build where clause for specific event
    $event_where = $where_clause;
    if ($where_clause == "") {
        $event_where = "WHERE event = '$event_name'";
    } else {
        $event_where .= " AND event = '$event_name'";
    }
    
    // Get data for this event
    $event_masuk = mysqli_fetch_array(mysqli_query(
        $koneksi, "SELECT SUM(jumlah) AS total FROM kas_masuk $event_where"
    ))['total'] ?? 0;
    
    $event_keluar = mysqli_fetch_array(mysqli_query(
        $koneksi, "SELECT SUM(jumlah) AS total FROM kas_keluar $event_where"
    ))['total'] ?? 0;
    
    $event_saldo = $event_masuk - $event_keluar;
    $profit_class = $event_saldo >= 0 ? 'profit-positive' : 'profit-negative';
?>

<!-- EVENT HEADER -->
<table>
    <tr>
        <td colspan="5" class="event-header">
            🎉 EVENT #<?= $event_number++ ?>: <?= strtoupper($event_name) ?>
        </td>
    </tr>
</table>

<!-- EVENT SUMMARY -->
<table>
    <tr>
        <th style="background-color: #3b82f6;">Kas Masuk</th>
        <th style="background-color: #3b82f6;">Kas Keluar</th>
        <th style="background-color: #3b82f6;">Profit/Loss</th>
    </tr>
    <tr>
        <td style="background-color: #d1fae5;">Rp <?= number_format($event_masuk, 0, ',', '.') ?></td>
        <td style="background-color: #fee2e2;">Rp <?= number_format($event_keluar, 0, ',', '.') ?></td>
        <td class="<?= $profit_class ?>">
            Rp <?= number_format(abs($event_saldo), 0, ',', '.') ?>
            <?= $event_saldo >= 0 ? '(PROFIT)' : '(LOSS)' ?>
        </td>
    </tr>
</table>

<br>

<!-- DETAIL KAS MASUK - EVENT -->
<table>
    <thead>
        <tr>
            <th colspan="5" class="header-green">💰 DETAIL KAS MASUK - <?= strtoupper($event_name) ?></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Event</th>
            <th>Keterangan</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $data_masuk = mysqli_query($koneksi, "SELECT * FROM kas_masuk $event_where ORDER BY tanggal DESC");
        $has_data = false;
        while ($d = mysqli_fetch_array($data_masuk)) {
            $has_data = true;
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
            <td><?= $d['event'] ?></td>
            <td><?= $d['keterangan'] ?></td>
            <td>Rp <?= number_format($d['jumlah'], 0, ',', '.') ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!$has_data): ?>
        <tr>
            <td colspan="5">Tidak ada data kas masuk</td>
        </tr>
        <?php endif; ?>
        
        <tr class="total-row">
            <th colspan="4" style="text-align: right;">TOTAL KAS MASUK</th>
            <th>Rp <?= number_format($event_masuk, 0, ',', '.') ?></th>
        </tr>
    </tbody>
</table>

<br>

<!-- DETAIL KAS KELUAR - EVENT -->
<table>
    <thead>
        <tr>
            <th colspan="5" class="header-red">💸 DETAIL KAS KELUAR - <?= strtoupper($event_name) ?></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Event</th>
            <th>Keterangan</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $data_keluar = mysqli_query($koneksi, "SELECT * FROM kas_keluar $event_where ORDER BY tanggal DESC");
        $has_data = false;
        while ($d = mysqli_fetch_array($data_keluar)) {
            $has_data = true;
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
            <td><?= $d['event'] ?></td>
            <td><?= $d['keterangan'] ?></td>
            <td>Rp <?= number_format($d['jumlah'], 0, ',', '.') ?></td>
        </tr>
        <?php } ?>
        
        <?php if (!$has_data): ?>
        <tr>
            <td colspan="5">Tidak ada data kas keluar</td>
        </tr>
        <?php endif; ?>
        
        <tr class="total-row">
            <th colspan="4" style="text-align: right;">TOTAL KAS KELUAR</th>
            <th>Rp <?= number_format($event_keluar, 0, ',', '.') ?></th>
        </tr>
    </tbody>
</table>

<br><hr><br>

<div class="page-break"></div>

<?php endwhile; ?>

<!-- ===== REKAPITULASI AKHIR SEMUA EVENT ======= -->
<table>
    <tr>
        <th colspan="5" style="background-color: #7c3aed; color: white; font-size: 16px;">
            📈 REKAPITULASI SEMUA EVENT
        </th>
    </tr>
    <tr>
        <th>No</th>
        <th>Nama Event</th>
        <th>Total Masuk</th>
        <th>Total Keluar</th>
        <th>Profit/Loss</th>
    </tr>
    <?php
    // Reset pointer
    mysqli_data_seek($events_list, 0);
    $no = 1;
    $total_all_masuk = 0;
    $total_all_keluar = 0;
    
    while ($event_data = mysqli_fetch_array($events_list)):
        $event_name = $event_data['event'];
        
        $event_where = $where_clause;
        if ($where_clause == "") {
            $event_where = "WHERE event = '$event_name'";
        } else {
            $event_where .= " AND event = '$event_name'";
        }
        
        $event_masuk = mysqli_fetch_array(mysqli_query(
            $koneksi, "SELECT SUM(jumlah) AS total FROM kas_masuk $event_where"
        ))['total'] ?? 0;
        
        $event_keluar = mysqli_fetch_array(mysqli_query(
            $koneksi, "SELECT SUM(jumlah) AS total FROM kas_keluar $event_where"
        ))['total'] ?? 0;
        
        $event_profit = $event_masuk - $event_keluar;
        $total_all_masuk += $event_masuk;
        $total_all_keluar += $event_keluar;
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td style="text-align: left; font-weight: bold;"><?= $event_name ?></td>
        <td style="background-color: #d1fae5;">Rp <?= number_format($event_masuk, 0, ',', '.') ?></td>
        <td style="background-color: #fee2e2;">Rp <?= number_format($event_keluar, 0, ',', '.') ?></td>
        <td style="background-color: <?= $event_profit >= 0 ? '#d1fae5' : '#fee2e2' ?>; font-weight: bold;">
            Rp <?= number_format(abs($event_profit), 0, ',', '.') ?>
            <?= $event_profit >= 0 ? '✓' : '✗' ?>
        </td>
    </tr>
    <?php endwhile; ?>
    
    <tr style="background-color: #1f2937; color: white; font-weight: bold;">
        <th colspan="2">GRAND TOTAL</th>
        <th>Rp <?= number_format($total_all_masuk, 0, ',', '.') ?></th>
        <th>Rp <?= number_format($total_all_keluar, 0, ',', '.') ?></th>
        <th style="background-color: <?= ($total_all_masuk - $total_all_keluar) >= 0 ? '#10b981' : '#ef4444' ?>;">
            Rp <?= number_format(abs($total_all_masuk - $total_all_keluar), 0, ',', '.') ?>
        </th>
    </tr>
</table>

<br><br>

<!-- FOOTER INFO -->
<table>
    <tr>
        <td colspan="2" style="background-color: #f3f4f6; text-align: center;">
            <strong>Laporan ini dibuat secara otomatis oleh Sistem Keuangan WO</strong><br>
            Tanggal: <?= date('d F Y H:i:s') ?><br>
            <?php if ($filter_bulan || $filter_event): ?>
                Filter: 
                <?php if ($filter_bulan): ?>
                    Bulan <?= date('F Y', strtotime($filter_bulan . '-01')) ?>
                <?php endif; ?>
                <?php if ($filter_event): ?>
                    | Event: <?= $filter_event ?>
                <?php endif; ?>
            <?php else: ?>
                Semua Data
            <?php endif; ?>
        </td>
    </tr>
</table>

</body>
</html>