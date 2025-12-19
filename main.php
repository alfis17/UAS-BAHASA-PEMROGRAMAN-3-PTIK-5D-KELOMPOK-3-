<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'Sistem Pembayaran SPP' ?></title>
    
    <!-- Di bagian <head> layout/main.php -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
            padding-right: 0 !important; /* Mencegah scrollbar issues */
        }
        
        /* SIDEBAR - TURUNKAN Z-INDEX */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #1a252f 100%);
            color: white;
            position: fixed;
            width: 250px;
            padding-top: 20px;
            z-index: 100; /* TURUNKAN DARI 1030 KE 100 */
            overflow-y: auto;
            height: 100vh;
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            overflow-x: auto;
            width: calc(100% - 250px);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            border-left: 4px solid var(--secondary-color);
        }
        
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .table th {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .badge-paid {
            background-color: var(--success-color);
        }
        
        .badge-unpaid {
            background-color: var(--danger-color);
        }
        
        /* MODAL FIX - PASTIKAN LEBIH TINGGI */
        .modal {
            z-index: 1060 !important;
        }
        
        .modal-backdrop {
            z-index: 1050 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }
        
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: white;
        }
        
        .table-container table {
            margin-bottom: 0;
            min-width: 800px;
        }
        
        .table-container::-webkit-scrollbar {
            height: 8px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        .table-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: static;
                min-height: auto;
                height: auto;
                z-index: 100;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
                overflow-x: visible;
            }
            
            .table-container {
                min-width: 100%;
            }
            
            .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }
        }
    </style>
</head>
<body>
    <!-- Container utama -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="sidebar d-none d-md-block">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">SPP SYSTEM</h4>
                    <p class="text-muted small">Sistem Pembayaran SPP</p>
                </div>
                
                <div class="px-3">
                    <div class="user-info text-center mb-4 p-3 bg-dark rounded">
                        <div class="mb-2">
                            <i class="bi bi-person-circle fs-1"></i>
                        </div>
                        <h6><?= session()->get('nama_lengkap') ?></h6>
                        <small class="text-muted"><?= session()->get('role') ?></small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= (current_url() == base_url('/dashboard')) ? 'active' : '' ?>" href="<?= base_url('/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(current_url(), 'siswa') !== false ? 'active' : '' ?>" href="<?= base_url('/siswa') ?>">
                                <i class="bi bi-people"></i> Data Siswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(current_url(), 'kelas') !== false ? 'active' : '' ?>" href="<?= base_url('/kelas') ?>">
                                <i class="bi bi-building"></i> Data Kelas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(current_url(), 'spp') !== false ? 'active' : '' ?>" href="<?= base_url('/spp') ?>">
                                <i class="bi bi-cash-coin"></i> Data SPP
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(current_url(), 'pembayaran') !== false ? 'active' : '' ?>" href="<?= base_url('/pembayaran') ?>">
                                <i class="bi bi-credit-card"></i> Pembayaran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(current_url(), 'report') !== false ? 'active' : '' ?>" href="<?= base_url('/admin/index_laporan') ?>">
                                <i class="bi bi-file-earmark-text"></i> Laporan
                            </a>
                        </li>
                       
                        <li class="nav-item mt-4">
                              <a class="nav-link text-danger" href="<?= base_url('/logout') ?>"
                                 onclick="return confirm('Yakin ingin logout dari sistem?')">
                                  <i class="bi bi-box-arrow-right"></i> Logout
                              </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="main-content">
                <!-- Navbar Mobile -->
                <nav class="navbar navbar-dark bg-primary d-md-none mb-3">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="#">SPP SYSTEM</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMobile">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarMobile">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url('/dashboard') ?>">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url('/siswa') ?>">Data Siswa</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url('/kelas') ?>">Data Kelas</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url('/spp') ?>">Data SPP</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url('/pembayaran') ?>">Pembayaran</a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link" href="<?= base_url('/index_laporan') ?>">Laporan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-danger" href="<?= base_url('/logout') ?>"
                                    onclick="return confirm('Yakin ingin logout?')">Logout</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <div class="container-fluid">
                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?= $this->renderSection('content') ?>
                </div>
                
                <!-- Footer -->
                <footer class="mt-5 pt-3 border-top">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted">&copy; <?= date('Y') ?> Sistem Pembayaran SPP - UAS Pemrograman III</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p class="text-muted">Kelompok: Muhammad Alfis Azis, Fauzan Ikhwan, Wahyu Dwi Sasongko</p>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                scrollX: true,
                responsive: true
            });
            
            // FIX CRITICAL: Modal z-index fix
            $(document).on('show.bs.modal', '.modal', function() {
                // Set modal z-index tinggi
                $(this).css('z-index', 1060);
                
                // Set backdrop z-index
                $('.modal-backdrop').css('z-index', 1050);
                
                // Nonaktifkan scroll body
                $('body').addClass('modal-open');
                $('body').css('padding-right', '0');
                
                console.log('Modal opened with z-index:', $(this).css('z-index'));
            });
            
            $(document).on('hidden.bs.modal', '.modal', function() {
                // Aktifkan scroll body
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
                
                // Hapus backdrop jika masih ada
                $('.modal-backdrop').remove();
            });
            
            // Force remove any existing backdrop
            $('button[data-bs-toggle="modal"]').click(function() {
                // Hapus backdrop lama jika ada
                $('.modal-backdrop').remove();
                
                // Hapus modal-open class
                $('body').removeClass('modal-open');
            });
        });
        
        // Theme Toggle
        const themeToggle = document.createElement('button');
        themeToggle.className = 'btn btn-sm btn-outline-secondary position-fixed bottom-0 end-0 m-3';
        themeToggle.innerHTML = '<i class="bi bi-moon"></i>';
        themeToggle.onclick = function() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', newTheme);
            this.innerHTML = newTheme === 'dark' ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';
        };
        document.body.appendChild(themeToggle);
        
        // Emergency fix: Click handler untuk modal
        $(document).on('click', '[data-bs-toggle="modal"]', function(e) {
            e.preventDefault();
            
            // Hapus semua backdrop
            $('.modal-backdrop').remove();
            
            // Target modal
            var target = $(this).data('bs-target');
            
            // Tampilkan modal
            $(target).modal({
                backdrop: true,
                keyboard: true
            });
            
            // Force show
            $(target).modal('show');
            
            // Fix z-index
            setTimeout(function() {
                $(target).css('z-index', 1060);
                $('.modal-backdrop').css('z-index', 1050);
            }, 10);
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>