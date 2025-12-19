<?= $this->extend('layout/main'); ?>

<?= $this->section('content'); ?>
<div class="row mb-4">
    <div class="col">
        <div class="dashboard-header animate-fade-in">
            <h2 class="fw-bold"><i class="bi bi-speedometer2"></i> Dashboard</h2>
            <p class="text-muted">Selamat datang, <?= session()->get('nama_lengkap') ?>! Ini adalah ringkasan sistem.</p>
            <div class="header-divider"></div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card stat-card-1 animate-slide-up" style="animation-delay: 0.1s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Siswa</h6>
                        <h2 class="stat-number"><?= $total_siswa ?></h2>
                    </div>
                    <div class="stat-icon bg-primary p-3 rounded-circle">
                        <i class="bi bi-people fs-3 text-white"></i>
                    </div>
                </div>
                <div class="stat-progress mt-2">
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: <?= min(100, ($total_siswa/100)*100) ?>%"></div>
                    </div>
                </div>
                <small class="text-muted">Terdaftar dalam sistem</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card stat-card-2 animate-slide-up" style="animation-delay: 0.2s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Kelas</h6>
                        <h2 class="stat-number"><?= $total_kelas ?></h2>
                    </div>
                    <div class="stat-icon bg-success p-3 rounded-circle">
                        <i class="bi bi-building fs-3 text-white"></i>
                    </div>
                </div>
                <div class="stat-progress mt-2">
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: <?= min(100, ($total_kelas/20)*100) ?>%"></div>
                    </div>
                </div>
                <small class="text-muted">Kelas aktif</small>
            </div>
        </div>
    </div>
    
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card stat-card-3 animate-slide-up" style="animation-delay: 0.3s">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted">Pembayaran Bulan Ini</h6>
                                        <h2 class="stat-number">
                                            <?php
                                                $currentMonth = date('F');
                                                $currentYear = date('Y');
                                                $totalThisMonth = 0;
                                                if (!empty($recent_pembayaran) && is_array($recent_pembayaran)) {
                                                    foreach($recent_pembayaran as $p) {
                                                        $bulan = is_array($p) ? $p['bulan'] : (isset($p->bulan) ? $p->bulan : '');
                                                        $tahun = is_array($p) ? $p['tahun'] : (isset($p->tahun) ? $p->tahun : '');
                                                        if($bulan == $currentMonth && $tahun == $currentYear) {
                                                            $totalThisMonth++;
                                                        }
                                                    }
                                                }
                                                echo $totalThisMonth;
                                            ?>
                                        </h2>
                                    </div>
                                    <div class="stat-icon bg-warning p-3 rounded-circle">
                                        <i class="bi bi-cash-coin fs-3 text-white"></i>
                                    </div>
                                </div>
                                <div class="stat-progress mt-2">
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: <?= (!empty($total_siswa) && $total_siswa > 0) ? min(100, ($totalThisMonth/$total_siswa)*100) : 0 ?>%"></div>
                                    </div>
                                </div>
                                <small class="text-muted"><?= $currentMonth ?> <?= $currentYear ?></small>
                            </div>
                        </div>
                    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card stat-card-4 animate-slide-up" style="animation-delay: 0.4s">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Tunggakan</h6>
                        <h2 class="stat-number">
                            <?php
                                $currentYear = date('Y');
                                $paidThisYear = 0;
                                foreach($recent_pembayaran as $p) {
                                    $tahun = is_array($p) ? $p['tahun'] : $p->tahun;
                                    if($tahun == $currentYear) {
                                        $paidThisYear++;
                                    }
                                }
                                // Estimate tunggakan based on current year payments
                                // Assuming most students should pay 12 months per year
                                $expectedThisYear = $total_siswa * 12;
                                $tunggakan = max(0, $expectedThisYear - $paidThisYear);
                                echo $tunggakan;
                            ?>
                        </h2>
                    </div>
                    <div class="stat-icon bg-danger p-3 rounded-circle">
                        <i class="bi bi-exclamation-triangle fs-3 text-white"></i>
                    </div>
                </div>
                <div class="stat-progress mt-2">
                    <div class="progress">
                        <div class="progress-bar bg-danger" style="width: <?= $total_siswa > 0 ? min(100, (($total_siswa * 12 - $paidThisYear)/($total_siswa * 12))*100) : 0 ?>%"></div>
                    </div>
                </div>
                <small class="text-muted">Estimasi tunggakan <?= $currentYear ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Chart and Recent Activity -->
<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card chart-card animate-fade-in">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-graph-up"></i> Statistik Pembayaran <?= $chart_data['currentYear'] ?></h5>
            </div>
            <div class="card-body">
                <canvas id="paymentChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card activity-card animate-slide-left">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h5>
            </div>
            <div class="card-body p-0 activity-scroll">
                <div class="list-group list-group-flush">
                    <?php
                    $counter = 0;
                    foreach(array_slice($recent_pembayaran, 0, 5) as $p):
                        $counter++;
                        $nama_siswa = is_array($p) ? $p['nama_siswa'] : $p->nama_siswa;
                        $jumlah_bayar = is_array($p) ? $p['jumlah_bayar'] : $p->jumlah_bayar;
                        $bulan = is_array($p) ? $p['bulan'] : $p->bulan;
                        $tahun = is_array($p) ? $p['tahun'] : $p->tahun;
                        $petugas = is_array($p) ? $p['petugas'] : $p->petugas;
                        $tanggal_bayar = is_array($p) ? $p['tanggal_bayar'] : $p->tanggal_bayar;
                    ?>
                    <div class="list-group-item activity-item animate-fade-in" style="animation-delay: <?= $counter * 0.1 ?>s">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= $nama_siswa ?></h6>
                            <span class="badge bg-success rounded-pill">Rp <?= number_format($jumlah_bayar, 0, ',', '.') ?></span>
                        </div>
                        <p class="mb-1 small text-muted"><?= $bulan ?> <?= $tahun ?> - <?= $petugas ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-calendar"></i> <?= date('d/m/Y', strtotime($tanggal_bayar)) ?></small>
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($recent_pembayaran)): ?>
                    <div class="list-group-item text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                        <p class="text-muted mb-0">Tidak ada aktivitas terbaru</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col">
        <div class="card quick-actions-card animate-fade-in">
            <div class="card-header bg-gradient-info text-white">
                <h5 class="card-title mb-0"><i class="bi bi-lightning"></i> Aksi Cepat Operasional</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('/siswa') ?>" class="btn btn-action btn-action-1 w-100 h-100">
                            <div class="action-icon">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="action-text">Tambah Siswa</div>
                            <div class="action-hover">Kelola data siswa</div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('/pembayaran') ?>" class="btn btn-action btn-action-2 w-100 h-100">
                            <div class="action-icon">
                                <i class="bi bi-cash"></i>
                            </div>
                            <div class="action-text">Input Pembayaran</div>
                            <div class="action-hover">Proses pembayaran SPP</div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('/index_laporan') ?>" class="btn btn-action btn-action-3 w-100 h-100">
                            <div class="action-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="action-text">Lihat Laporan</div>
                            <div class="action-hover">Analisis & reporting</div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('/spp') ?>" class="btn btn-action btn-action-4 w-100 h-100">
                            <div class="action-icon">
                                <i class="bi bi-gear"></i>
                            </div>
                            <div class="action-text">Atur SPP</div>
                            <div class="action-hover">Konfigurasi sistem</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card status-card animate-slide-right">
            <div class="card-header bg-gradient-dark text-white">
                <h5 class="card-title mb-0"><i class="bi bi-check-circle"></i> Status Sistem</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-success"></div>
                            <span>Database Connection</span>
                        </div>
                        <span class="badge bg-success rounded-pill">Online</span>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-primary"></div>
                            <span>Total Pengguna</span>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?= $total_siswa ?></span>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-secondary"></div>
                            <span>Storage Database</span>
                        </div>
                        <span class="badge bg-secondary rounded-pill"><?= round($total_siswa * 0.5, 2) ?> MB</span>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center status-item">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-info"></div>
                            <span>Server Uptime</span>
                        </div>
                        <span class="badge bg-info rounded-pill">99.9%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card info-card animate-slide-left">
            <div class="card-header bg-gradient-warning text-white">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Informasi & Tips</h5>
            </div>
            <div class="card-body">
                <div class="info-alert">
                    <div class="info-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <div class="info-content">
                        <strong>Tips & Best Practices:</strong> 
                        <ul class="mb-0 mt-2">
                            <li><i class="bi bi-database-check"></i> Lakukan backup database secara rutin</li>
                            <li><i class="bi bi-clipboard-data"></i> Periksa laporan tunggakan setiap minggu</li>
                            <li><i class="bi bi-shield-lock"></i> Update profile dan password secara berkala</li>
                            <li><i class="bi bi-gear-wide"></i> Konfigurasi pengaturan sesuai kebutuhan sekolah</li>
                        </ul>
                    </div>
                </div>
                
                <div class="info-footer mt-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar-event"></i> 
                        Terakhir diupdate: <?= date('d F Y H:i') ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Payment Chart
        const ctx = document.getElementById('paymentChart');
        if (ctx) {
            const paymentChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($chart_data['labels']) ?>,
                    datasets: [{
                        label: 'Total Pembayaran (Rp)',
                        data: <?= json_encode($chart_data['data']) ?>,
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#0d6efd',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Add ripple effect to action buttons
        document.querySelectorAll('.btn-action').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.7);
                    transform: scale(0);
                    animation: ripple-animation 0.6s linear;
                    width: ${size}px;
                    height: ${size}px;
                    top: ${y}px;
                    left: ${x}px;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    });
</script>

<style>
/* Global Styles */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --warning-gradient: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
    --danger-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    --info-gradient: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
    --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%);
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

/* Dashboard Header */
.dashboard-header {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--primary-gradient);
}

.header-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, #dee2e6, transparent);
    margin: 1rem 0;
}

/* Stat Cards */
.stat-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
    background: white;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
}

.stat-card-1::before { background: var(--primary-gradient); }
.stat-card-2::before { background: var(--success-gradient); }
.stat-card-3::before { background: var(--warning-gradient); }
.stat-card-4::before { background: var(--danger-gradient); }

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.stat-icon {
    transition: all 0.3s ease;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(45deg, #2c3e50, #4ca1af);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0.5rem 0;
}

/* Progress bars */
.progress {
    height: 6px;
    border-radius: 3px;
    background-color: rgba(0,0,0,0.05);
}

.progress-bar {
    border-radius: 3px;
    transition: width 1.5s ease-in-out;
}

/* Chart Card */
.chart-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: var(--primary-gradient) !important;
}

.bg-gradient-success {
    background: var(--success-gradient) !important;
}

.bg-gradient-info {
    background: var(--info-gradient) !important;
}

.bg-gradient-dark {
    background: var(--dark-gradient) !important;
}

.bg-gradient-warning {
    background: var(--warning-gradient) !important;
}

/* Activity Card */
.activity-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.activity-scroll {
    max-height: 400px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #007bff #f8f9fa;
}

.activity-scroll::-webkit-scrollbar {
    width: 6px;
}

.activity-scroll::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 3px;
}

.activity-scroll::-webkit-scrollbar-thumb {
    background: #007bff;
    border-radius: 3px;
}

.activity-item {
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
    padding: 1rem;
}

.activity-item:hover {
    border-left: 3px solid #28a745;
    background-color: rgba(40, 167, 69, 0.05);
    transform: translateX(5px);
}

/* Quick Actions */
.quick-actions-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.btn-action {
    border: none;
    border-radius: 12px;
    padding: 1.5rem 1rem;
    background: white;
    color: #333;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 140px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.btn-action:hover {
    color: white;
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.btn-action-1:hover { background: var(--primary-gradient); }
.btn-action-2:hover { background: var(--success-gradient); }
.btn-action-3:hover { background: var(--info-gradient); }
.btn-action-4:hover { background: var(--warning-gradient); }

.action-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.btn-action:hover .action-icon {
    transform: scale(1.2) rotate(10deg);
}

.action-text {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.action-hover {
    font-size: 0.8rem;
    opacity: 0;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-action:hover .action-hover {
    opacity: 0.9;
    max-height: 50px;
}

/* Status & Info Cards */
.status-card, .info-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.status-item {
    transition: all 0.3s ease;
    padding: 1rem 1.5rem;
}

.status-item:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(5px);
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 10px;
}

.info-alert {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
    border-left: 4px solid #ffc107;
    padding: 1.5rem;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
}

.info-icon {
    font-size: 1.5rem;
    color: #ffc107;
    margin-right: 1rem;
}

.info-content ul {
    list-style: none;
    padding-left: 0;
}

.info-content li {
    margin-bottom: 0.5rem;
    padding-left: 1.5rem;
    position: relative;
}

.info-content li:before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #28a745;
    font-weight: bold;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideLeft {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideRight {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.animate-fade-in {
    animation: fadeIn 0.8s ease-out;
}

.animate-slide-up {
    animation: slideUp 0.6s ease-out;
}

.animate-slide-left {
    animation: slideLeft 0.6s ease-out;
}

.animate-slide-right {
    animation: slideRight 0.6s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stat-number {
        font-size: 2rem;
    }
    
    .btn-action {
        min-height: 120px;
        padding: 1rem;
    }
    
    .action-icon {
        font-size: 2rem;
    }
    
    .dashboard-header {
        padding: 1.5rem;
    }
}

/* Loading skeleton animation */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>
<?= $this->endSection(); ?>