<?php
include "config/koneksi.php";
include "layout/header.php";

// Filter parameters
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$filter_event = isset($_GET['event']) ? $_GET['event'] : '';

// Build where clause
$where_conditions = [];
if ($filter_bulan) {
    $where_conditions[] = "DATE_FORMAT(tanggal, '%Y-%m') = '$filter_bulan'";
}
if ($filter_event) {
    $where_conditions[] = "event LIKE '%$filter_event%'";
}
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get statistics
$masuk = mysqli_fetch_array(mysqli_query(
    $koneksi, "SELECT SUM(jumlah) AS total FROM kas_masuk $where_clause"
))['total'] ?? 0;

$keluar = mysqli_fetch_array(mysqli_query(
    $koneksi, "SELECT SUM(jumlah) AS total FROM kas_keluar $where_clause"
))['total'] ?? 0;

$saldo = $masuk - $keluar;

$total_transaksi_masuk = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kas_masuk $where_clause"));
$total_transaksi_keluar = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kas_keluar $where_clause"));

// Get event list
$event_list = mysqli_query($koneksi, "
    SELECT DISTINCT event FROM (
        SELECT event FROM kas_masuk
        UNION
        SELECT event FROM kas_keluar
    ) as events
    ORDER BY event ASC
");

// Grouping data per event
$grouping_data = mysqli_query($koneksi, "
    SELECT 
        event,
        SUM(CASE WHEN tipe = 'Masuk' THEN jumlah ELSE 0 END) as total_masuk,
        SUM(CASE WHEN tipe = 'Keluar' THEN jumlah ELSE 0 END) as total_keluar,
        COUNT(*) as jumlah_transaksi
    FROM (
        SELECT 'Masuk' as tipe, event, jumlah, tanggal FROM kas_masuk $where_clause
        UNION ALL
        SELECT 'Keluar' as tipe, event, jumlah, tanggal FROM kas_keluar $where_clause
    ) as combined
    GROUP BY event
    ORDER BY event ASC
");

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$total_data_query = mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM (
        SELECT tanggal FROM kas_masuk $where_clause
        UNION ALL 
        SELECT tanggal FROM kas_keluar $where_clause
    ) as combined
");
$total_data = mysqli_fetch_array($total_data_query)['total'];
$total_pages = ceil($total_data / $limit);

$transaksi_terbaru = mysqli_query($koneksi, "
    (SELECT 'Masuk' as tipe, tanggal, event, keterangan, jumlah FROM kas_masuk $where_clause)
    UNION ALL
    (SELECT 'Keluar' as tipe, tanggal, event, keterangan, jumlah FROM kas_keluar $where_clause)
    ORDER BY tanggal DESC
    LIMIT $start, $limit
");

$query_params = [];
if ($filter_bulan) $query_params[] = "bulan=$filter_bulan";
if ($filter_event) $query_params[] = "event=" . urlencode($filter_event);
$query_string = !empty($query_params) ? '&' . implode('&', $query_params) : '';
?>

<div class="container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h3>Dashboard Keuangan</h3>
        <div class="dashboard-date">
            <?php
            setlocale(LC_TIME, 'id_ID.UTF-8');
            echo strftime('%A, %d %B %Y');
            ?>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <div class="filter-header">
            <h4>Filter Data</h4>
            <?php if ($filter_bulan || $filter_event): ?>
                <a href="dashboard.php" class="btn-reset-filter">Reset Filter</a>
            <?php endif; ?>
        </div>
        
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label>Bulan</label>
                <input class="full" type="month" name="bulan" value="<?= $filter_bulan ?>" onchange="this.form.submit()">
            </div>
            
            <div class="filter-group">
                <label>Event</label>
                <select class="full" name="event" onchange="this.form.submit()">
                    <option value="">Semua Event</option>
                    <?php while ($evt = mysqli_fetch_array($event_list)): ?>
                        <option value="<?= $evt['event'] ?>" <?= ($filter_event == $evt['event']) ? 'selected' : '' ?>>
                            <?= $evt['event'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
        
        <?php if ($filter_bulan || $filter_event): ?>
            <div class="filter-active-info">
                <strong>Filter Aktif:</strong>
                <?php if ($filter_bulan): ?>
                    <span class="filter-badge"><?= date('F Y', strtotime($filter_bulan . '-01')) ?></span>
                <?php endif; ?>
                <?php if ($filter_event): ?>
                    <span class="filter-badge"><?= $filter_event ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <div class="card card-masuk">
            <h4>Kas Masuk</h4>
            <div class="value">Rp <?= number_format($masuk, 0, ',', '.') ?></div>
            <div class="subtitle">
                <span class="count"><?= $total_transaksi_masuk ?></span> transaksi
            </div>
        </div>
        
        <div class="card card-keluar">
            <h4>Kas Keluar</h4>
            <div class="value">Rp <?= number_format($keluar, 0, ',', '.') ?></div>
            <div class="subtitle">
                <span class="count"><?= $total_transaksi_keluar ?></span> transaksi
            </div>
        </div>
        
        <div class="card card-saldo">
            <h4>Saldo Akhir</h4>
            <div class="value">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
            <div class="subtitle">
                <span class="status <?= $saldo >= 0 ? 'positive' : 'negative' ?>">
                    <?= $saldo >= 0 ? 'Positif' : 'Negatif' ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Grouping Per Event -->
    <div class="grouping-container">
        <div class="grouping-header">
            <h3>Ringkasan Per Event</h3>
            <button onclick="toggleGrouping()" class="btn-toggle" id="btnToggle">
                Tampilkan
            </button>
        </div>
        
        <div class="grouping-content" id="groupingContent" style="display: none;">
            <?php 
            $no = 1;
            $has_data = false;
            while ($group = mysqli_fetch_array($grouping_data)): 
                $has_data = true;
                $profit = $group['total_masuk'] - $group['total_keluar'];
                $profit_class = $profit >= 0 ? 'profit-positive' : 'profit-negative';
            ?>
                <div class="event-group-card">
                    <div class="event-group-header">
                        <div class="event-number"><?= $no++ ?></div>
                        <div class="event-title">
                            <h4><?= $group['event'] ?></h4>
                            <span class="event-transaksi"><?= $group['jumlah_transaksi'] ?> transaksi</span>
                        </div>
                    </div>
                    
                    <div class="event-group-stats">
                        <div class="stat-item stat-masuk">
                            <span class="stat-label">Masuk</span>
                            <span class="stat-value">Rp <?= number_format($group['total_masuk'], 0, ',', '.') ?></span>
                        </div>
                        
                        <div class="stat-item stat-keluar">
                            <span class="stat-label">Keluar</span>
                            <span class="stat-value">Rp <?= number_format($group['total_keluar'], 0, ',', '.') ?></span>
                        </div>
                        
                        <div class="stat-item <?= $profit_class ?>">
                            <span class="stat-label"><?= $profit >= 0 ? 'Profit' : 'Loss' ?></span>
                            <span class="stat-value">Rp <?= number_format(abs($profit), 0, ',', '.') ?></span>
                        </div>
                    </div>
                    
                    <a href="?event=<?= urlencode($group['event']) ?>" class="btn-view-detail">
                        Lihat Detail
                    </a>
                </div>
            <?php endwhile; ?>
            
            <?php if (!$has_data): ?>
                <div class="no-data-message">
                    <div class="no-data-icon">📭</div>
                    <p>Tidak ada data</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="transaction-container">
        <div class="transaction-header">
            <h3>Riwayat Transaksi</h3>
            <div class="transaction-info">
                <?= min($start + 1, $total_data) ?> - <?= min($start + $limit, $total_data) ?> dari <?= $total_data ?>
            </div>
        </div>
        
        <div class="transaction-table-container">
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Event</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = $start + 1;
                    $has_transaction = false;
                    while ($trans = mysqli_fetch_array($transaksi_terbaru)): 
                        $has_transaction = true;
                    ?>
                        <tr class="table-row-<?= strtolower($trans['tipe']) ?>">
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($trans['tanggal'])) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($trans['tipe']) ?>">
                                    <?= $trans['tipe'] ?>
                                </span>
                            </td>
                            <td><?= $trans['event'] ?></td>
                            <td><?= $trans['keterangan'] ?></td>
                            <td class="amount-<?= strtolower($trans['tipe']) ?>">
                                <?= $trans['tipe'] == 'Masuk' ? '+' : '-' ?>Rp <?= number_format($trans['jumlah'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    
                    <?php if (!$has_transaction): ?>
                        <tr>
                            <td colspan="6">
                                <div class="no-data-message">
                                    <div class="no-data-icon">🔭</div>
                                    <p>Belum ada transaksi</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <div class="pagination-info">
                Halaman <?= $page ?> dari <?= $total_pages ?>
            </div>
            <div class="pagination-buttons">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $query_string ?>" class="page-btn">Sebelumnya</a>
                <?php endif; ?>
                
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="?page=<?= $i ?><?= $query_string ?>" 
                       class="page-btn <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $query_string ?>" class="page-btn">Selanjutnya</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleGrouping() {
    const content = document.getElementById('groupingContent');
    const btn = document.getElementById('btnToggle');
    
    if (content.style.display === 'none') {
        content.style.display = 'grid';
        btn.textContent = 'Sembunyikan';
    } else {
        content.style.display = 'none';
        btn.textContent = 'Tampilkan';
    }
}
</script>

<?php include "layout/footer.php"; ?>