<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violet Hospital - Sistem Informasi Manajemen RS (Online)</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- LIBRARIES -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SUPABASE CLIENT -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    
    <style>
        :root {
            --primary: #6f42c1; --secondary: #6c757d; --success: #198754;
            --info: #0dcaf0; --warning: #ffc107; --danger: #dc3545;
            --light: #f8f9fa; --dark: #212529;
        }
        body { font-family: 'Segoe UI', sans-serif; padding-top: 70px; background-color: #f4f6f9; }
        
        .navbar { box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: rgba(255,255,255,0.95); backdrop-filter: blur(5px); }
        .navbar-brand { font-weight: 800; color: var(--primary) !important; letter-spacing: -0.5px; }
        .hero-section { background: linear-gradient(135deg, var(--primary) 0%, #4a2c89 100%); color: white; padding: 80px 0 60px; border-radius: 0 0 50px 50px; margin-bottom: 40px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .bed-card { cursor: pointer; border: 2px solid transparent; transition: all 0.2s; }
        .bed-avail { background: #d1e7dd; color: #0f5132; border-color: #badbcc; }
        .bed-occ { background: #f8d7da; color: #842029; border-color: #f5c2c7; }
        .bed-clean { background: #fff3cd; color: #664d03; border-color: #ffecb5; }
        .queue-container { background: #121212; color: #e0e0e0; min-height: 100vh; padding: 30px; position: fixed; top: 0; left: 0; width: 100%; z-index: 9999; overflow-y: auto; }
        .queue-box { background: #1e1e1e; border: 1px solid #333; border-radius: 15px; padding: 30px; text-align: center; margin-bottom: 20px; }
        .q-num { font-size: 5rem; font-weight: 800; color: var(--info); line-height: 1; }
        .q-blink { animation: blink 1s infinite; color: var(--warning); }
        @keyframes blink { 50% { opacity: 0.3; } }
        .receipt-box { font-family: 'Courier New', monospace; border: 2px dashed #333; padding: 20px; background: #fff; color: #000; }
        .patient-card-box { width: 400px; height: 240px; border-radius: 15px; background: linear-gradient(135deg, #6f42c1, #a66efa); color: white; padding: 25px; position: relative; overflow: hidden; margin: auto; }
        .patient-card-box::after { content: '\f481'; font-family: "Font Awesome 6 Free"; position: absolute; right: -30px; bottom: -30px; font-size: 150px; opacity: 0.15; font-weight: 900; }
        .hidden { display: none !important; }
    </style>
</head>
<body>

    <!-- NAVIGATION -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#" onclick="nav('home')">
                <i class="fas fa-hospital-user me-2"></i>VIOLET HOSPITAL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#" onclick="nav('home')">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="openQueueTV()">Layar Antrian</a></li>
                    
                    <li class="nav-item role-admin hidden"><a class="nav-link fw-bold text-danger" href="#" onclick="nav('admin')">Admin</a></li>
                    <li class="nav-item role-doctor hidden"><a class="nav-link fw-bold text-success" href="#" onclick="nav('doctor')">Dokter</a></li>
                    <li class="nav-item role-user hidden"><a class="nav-link fw-bold text-primary" href="#" onclick="nav('user')">Pasien</a></li>
                    
                    <li class="nav-item ms-3 ps-3 border-start">
                        <button id="btnLogin" class="btn btn-outline-primary btn-sm rounded-pill px-4" onclick="showAuthModal()">Login / Daftar</button>
                        <div id="userProfile" class="d-flex align-items-center hidden">
                            <div class="text-end me-2 lh-1"><small class="d-block fw-bold text-dark" id="uName">User</small><small class="text-muted" id="uRole">Role</small></div>
                            <button onclick="handleLogout()" class="btn btn-light text-danger btn-sm rounded-circle shadow-sm" title="Keluar"><i class="fas fa-power-off"></i></button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 1. HOME SECTION -->
    <section id="page-home">
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="display-4 fw-bold mb-3">Kesehatan Anda, Prioritas Kami</h1>
                <p class="lead mb-4 opacity-75">Sistem pelayanan kesehatan modern dengan pendaftaran online dan rekam medis terintegrasi.</p>
                <div class="d-flex justify-content-center gap-3">
                    <button id="heroAuthBtn" onclick="handleHeroAuth()" class="btn btn-light text-primary fw-bold btn-lg px-4 shadow">Masuk / Daftar</button>
                    <button onclick="openQueueTV()" class="btn btn-outline-light btn-lg px-4 fw-bold"><i class="fas fa-tv me-2"></i>Live Antrian</button>
                </div>
            </div>
        </div>
        
        <div class="container" style="position: relative; top: -40px; z-index: 2;">
            <div class="row g-4 justify-content-center mb-5">
                <div class="col-md-4"><div class="card p-4 text-center h-100"><div class="text-primary mb-3"><i class="fas fa-user-md fa-3x"></i></div><h5>Dokter Spesialis</h5><p class="text-muted small">Tim medis berpengalaman siap melayani.</p></div></div>
                <div class="col-md-4"><div class="card p-4 text-center h-100"><div class="text-success mb-3"><i class="fas fa-clock fa-3x"></i></div><h5>Layanan 24 Jam</h5><p class="text-muted small">IGD dan Farmasi siap setiap saat.</p></div></div>
                <div class="col-md-4"><div class="card p-4 text-center h-100"><div class="text-warning mb-3"><i class="fas fa-notes-medical fa-3x"></i></div><h5>Rekam Medis Digital</h5><p class="text-muted small">Riwayat kesehatan Anda tersimpan aman.</p></div></div>
            </div>
            <h3 class="text-center fw-bold mb-4 text-secondary">Layanan Poli</h3><div class="row g-4 justify-content-center mb-5" id="homePoliList"></div>
            <h3 class="text-center fw-bold mb-4 text-secondary">Jadwal Dokter Hari Ini</h3><div class="row g-4 mb-5" id="publicDoctorList"></div>
        </div>
    </section>

    <!-- 2. LAYAR ANTRIAN TV -->
    <div id="page-queue" class="queue-container hidden">
        <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-3">
            <h1 class="fw-bold text-white"><i class="fas fa-hospital me-3 text-primary"></i>ANTRIAN VIOLET HOSPITAL</h1>
            <div class="d-flex align-items-center gap-4"><span id="clock" class="fs-3 fw-bold font-monospace"></span><button onclick="closeQueueTV()" class="btn btn-outline-light btn-sm rounded-pill px-3">Tutup</button></div>
        </div>
        <div class="row g-4" id="queueListContainer"></div>
        <div class="text-center mt-5 text-secondary"><p class="fs-5">Harap menunggu nomor antrian Anda dipanggil. Terima kasih atas kesabaran Anda.</p></div>
    </div>

    <!-- 3. ADMIN DASHBOARD -->
    <section id="page-admin" class="container hidden">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-danger mb-0"><i class="fas fa-laptop-medical me-2"></i>Admin Dashboard</h3>
            <div><button onclick="resetSystem()" class="btn btn-outline-danger btn-sm me-2"><i class="fas fa-trash me-1"></i> Reset Data</button><span class="badge bg-danger rounded-pill px-3 py-2">Super User</span></div>
        </div>
        <div class="card p-2 mb-4 shadow-sm">
            <ul class="nav nav-pills nav-fill" role="tablist">
                <li class="nav-item"><button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tab-fo"><i class="fas fa-desktop me-2"></i>FO (Daftar)</button></li>
                <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-kasir" onclick="loadCashier()"><i class="fas fa-cash-register me-2"></i>Kasir</button></li>
                <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-pharma" onclick="loadPharma()"><i class="fas fa-pills me-2"></i>Farmasi</button></li>
                <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-bed" onclick="loadBeds()"><i class="fas fa-bed me-2"></i>Inap</button></li>
                <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-report" onclick="loadReports()"><i class="fas fa-chart-pie me-2"></i>Laporan</button></li>
                <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-master" onclick="loadMaster()"><i class="fas fa-database me-2"></i>Master</button></li>
            </ul>
        </div>
        <div class="tab-content">
            <!-- FO -->
            <div class="tab-pane fade show active" id="tab-fo"><div class="row justify-content-center"><div class="col-md-8"><div class="card shadow-sm border-0"><div class="card-header bg-primary text-white py-3"><h5 class="mb-0">Pendaftaran Walk-In (Offline)</h5></div><div class="card-body p-4"><form onsubmit="handleWalkIn(event)"><div class="row g-3"><div class="col-md-6"><label>Nama</label><input type="text" id="foName" class="form-control" required></div><div class="col-md-6"><label>NIK</label><input type="number" id="foNik" class="form-control" placeholder="16 digit" required oninput="if(this.value.length>16)this.value=this.value.slice(0,16)"></div><div class="col-md-4"><label>Umur</label><input type="number" id="foAge" class="form-control" required oninput="if(this.value.length>2)this.value=this.value.slice(0,2)"></div><div class="col-md-4"><label>Jaminan</label><select id="foIns" class="form-select"><option value="Non-BPJS">Umum</option><option value="BPJS">BPJS</option></select></div><div class="col-md-4"><label>Poli</label><select id="foPoli" class="form-select" required><option disabled selected>Pilih...</option></select></div></div><button type="submit" class="btn btn-primary w-100 mt-4 py-2 fw-bold"><i class="fas fa-print me-2"></i>Daftar & Cetak Karcis</button></form></div></div></div></div></div>
            <!-- Kasir -->
            <div class="tab-pane fade" id="tab-kasir"><div class="card shadow-sm"><div class="card-body"><div class="d-flex justify-content-between align-items-center mb-3"><h5>Tagihan Pending</h5><input type="text" id="searchCashier" class="form-control w-25 form-control-sm" placeholder="Cari..." onkeyup="loadCashier()"></div><div class="table-responsive"><table class="table table-hover align-middle"><thead class="table-light"><tr><th>No</th><th>Pasien</th><th>Jaminan</th><th>Total</th><th>Aksi</th></tr></thead><tbody id="cashierTableBody"></tbody></table><div id="cashierEmptyState" class="text-center py-5 text-muted d-none"><i class="fas fa-check-circle fa-3x mb-3 text-success"></i><p>Tidak ada tagihan pending.</p></div></div></div></div></div>
            <!-- Farmasi -->
            <div class="tab-pane fade" id="tab-pharma"><div class="row g-4"><div class="col-md-4"><div class="card shadow-sm h-100"><div class="card-header bg-success text-white">Input Obat</div><div class="card-body"><form onsubmit="addMedicine(event)"><div class="mb-3"><label>Nama</label><input type="text" id="newMedName" class="form-control" required></div><div class="mb-3"><label>Harga</label><input type="number" id="newMedPrice" class="form-control" required></div><div class="mb-3"><label>Stok</label><input type="number" id="newMedStock" class="form-control" required></div><div class="mb-3"><label>Status</label><select id="newMedStatus" class="form-select"><option value="Tersedia">Tersedia</option><option value="Habis">Habis</option></select></div><button class="btn btn-success w-100">Simpan</button></form></div></div></div><div class="col-md-8"><div class="card shadow-sm h-100"><div class="card-header bg-white d-flex justify-content-between"><span>Stok Obat</span><input type="text" id="searchPharma" class="form-control form-control-sm w-25" placeholder="Cari..." onkeyup="renderPharmacyTable()"></div><div class="card-body p-0"><div class="table-responsive" style="max-height: 400px;"><table class="table table-striped mb-0"><thead class="table-success sticky-top"><tr><th>Nama</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="pharmacyTableBody"></tbody></table></div></div></div></div></div></div>
            <!-- Inap -->
            <div class="tab-pane fade" id="tab-bed"><div class="card shadow-sm mb-3"><div class="card-body bg-light border rounded"><div class="d-flex align-items-center"><i class="fas fa-info-circle text-primary fs-4 me-3"></i><div><strong>Manajemen Bangsal:</strong> Klik kamar kosong untuk <b>Check-In</b>. Klik kamar terisi untuk <b>Check-Out</b>.</div></div></div></div><div class="row g-3" id="bedListContainer"></div></div>
            <!-- Laporan -->
            <div class="tab-pane fade" id="tab-report"><div class="row g-4 mb-4"><div class="col-md-4"><div class="card bg-success text-white h-100 p-3 text-center rounded-4 shadow-sm"><h3>Rp <span id="reportIncome">0</span></h3><small>Pendapatan Riil</small></div></div><div class="col-md-4"><div class="card bg-warning text-dark h-100 p-3 text-center rounded-4 shadow-sm"><h3>Rp <span id="reportBPJS">0</span></h3><small>Klaim BPJS</small></div></div><div class="col-md-4"><div class="card bg-info text-white h-100 p-3 text-center rounded-4 shadow-sm"><h3><span id="reportPatients">0</span></h3><small>Total Pasien</small></div></div></div><div class="row g-4 mb-4"><div class="col-md-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-bold">Statistik Poli</div><div class="card-body"><canvas id="poliChart"></canvas></div></div></div><div class="col-md-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-bold">Komposisi Pasien</div><div class="card-body"><canvas id="insuranceChart"></canvas></div></div></div></div><div class="card shadow-sm"><div class="card-header bg-white fw-bold">Riwayat Transaksi</div><div class="card-body p-0"><div class="table-responsive"><table class="table table-bordered mb-0"><thead><tr><th>Tanggal</th><th>Pasien</th><th>Poli</th><th>Tipe</th><th>Nominal</th></tr></thead><tbody id="reportTableBody"></tbody></table></div></div></div></div>
            <!-- Master -->
            <div class="tab-pane fade" id="tab-master"><div class="row"><div class="col-md-4"><div class="card shadow-sm mb-3"><div class="card-header bg-dark text-white">Registrasi Dokter Baru (HR)</div><div class="card-body"><form onsubmit="addDoctor(event)"><div class="mb-2"><input class="form-control" id="newDocName" placeholder="Nama Lengkap" required></div><div class="mb-2"><input class="form-control" id="newDocPoli" placeholder="Spesialisasi / Poli" required></div><div class="mb-2"><input class="form-control" id="newDocSchedule" placeholder="Jadwal Praktik" required></div><hr><div class="mb-2"><input class="form-control" id="newDocUser" placeholder="Username Login" required></div><div class="mb-3"><input type="text" class="form-control" id="newDocPass" placeholder="Password" required></div><button class="btn btn-dark w-100">Tambah Dokter</button></form></div></div></div><div class="col-md-8"><div class="card shadow-sm"><div class="card-header bg-white">Daftar Dokter</div><div class="card-body p-0"><table class="table table-striped mb-0"><thead class="table-dark"><tr><th>Nama</th><th>Poli</th><th>Jadwal</th><th>Aksi</th></tr></thead><tbody id="masterDoctorBody"></tbody></table></div></div></div></div></div>
        </div>
    </section>

    <!-- 4. DOCTOR DASHBOARD -->
    <section id="page-doctor" class="container hidden">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div><h3 class="fw-bold text-success mb-0">Ruang Praktik Dokter</h3><p class="text-muted mb-0" id="docNameDisplay">dr. ...</p></div>
            <span class="badge bg-success rounded-pill px-3 py-2" id="docPoliDisplay">Poli ...</span>
        </div>
        <div class="card shadow-sm border-0"><div class="card-header bg-success text-white py-3"><h5 class="mb-0">Antrian Pasien Hari Ini</h5></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-striped align-middle mb-0"><thead class="table-light"><tr><th>No</th><th>Data Pasien</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="doctorTableBody"></tbody></table><div id="doctorEmptyState" class="text-center py-5 text-muted d-none">Tidak ada pasien dalam antrian.</div></div></div></div>
    </section>

    <!-- 5. USER DASHBOARD -->
    <section id="page-user" class="container hidden">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div><h3 class="fw-bold text-primary mb-0">Area Pasien</h3><p class="text-muted mb-0">Selamat Datang, <strong id="userNameDisplay">User</strong></p></div>
            <div><button onclick="openProfileSettings()" class="btn btn-outline-secondary btn-sm me-2">Profil</button><button onclick="printPatientCard()" class="btn btn-outline-primary btn-sm">Kartu</button></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4"><div class="card h-100 bg-gradient-primary text-white border-0 p-4"><h3 class="mb-3">Daftar Berobat</h3><p class="opacity-75 mb-4">Ambil nomor antrian untuk pemeriksaan dokter hari ini.</p><button onclick="showSection('register')" class="btn btn-light text-primary fw-bold w-100 mt-auto shadow-sm">Daftar Sekarang</button></div></div>
            <div class="col-md-8"><div class="card h-100 shadow-sm border-0"><div class="card-header bg-white py-3 fw-bold">Riwayat Kunjungan</div><div class="card-body p-0 overflow-auto" style="max-height: 400px;"><ul class="list-group list-group-flush" id="userHistoryList"></ul><div id="userEmptyState" class="text-center py-5 text-muted d-none">Belum ada riwayat kunjungan.</div></div></div></div>
        </div>
    </section>

    <!-- 6. USER REGISTER FORM -->
    <section id="page-register" class="container hidden">
        <div class="row justify-content-center"><div class="col-md-8 col-lg-6"><div class="card shadow-lg border-0 mt-4"><div class="card-header bg-primary text-white text-center py-4"><h4 class="mb-0">Formulir Pendaftaran Online</h4></div><div class="card-body p-4"><div class="alert alert-primary d-flex align-items-center" role="alert"><i class="fas fa-user-circle fs-3 me-3"></i><div><strong>Konfirmasi Data:</strong><div id="patientInfoDisplay" class="small"></div></div></div><form onsubmit="handleRegistration(event)"><div class="mb-3"><label class="form-label">Poli Tujuan</label><select class="form-select" id="regPoli" required></select></div><div class="mb-3"><label class="form-label">Tanggal</label><input type="date" class="form-control" id="regDate" required></div><div class="mb-4"><label class="form-label">Keluhan</label><textarea class="form-control" id="regComplaint" rows="3" required></textarea></div><div class="d-grid gap-2"><button type="submit" class="btn btn-primary btn-lg shadow-sm">Ambil Antrian</button><button type="button" class="btn btn-light text-muted" onclick="showSection('user')">Batal</button></div></form></div></div></div></div>
    </section>

    <!-- AUTH MODAL -->
    <div class="modal fade" id="authModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Akses Sistem</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
        <ul class="nav nav-tabs mb-3"><li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#f-login">Login</a></li><li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#f-reg">Daftar Akun</a></li></ul>
        <div class="tab-content">
            <div class="tab-pane active" id="f-login"><form onsubmit="handleLogin(event)"><input class="form-control mb-2" id="lUser" placeholder="Username" required><input type="password" class="form-control mb-3" id="lPass" placeholder="Password" required><button class="btn btn-primary w-100">Masuk</button></form></div>
            <div class="tab-pane" id="f-reg"><form onsubmit="handleUserRegister(event)"><div class="row g-2 mb-2"><div class="col"><input class="form-control" id="rName" placeholder="Nama Lengkap" required></div><div class="col"><input type="number" class="form-control" id="rNik" placeholder="NIK (16 Digit)" required oninput="if(this.value.length>16)this.value=this.value.slice(0,16)"></div></div><div class="row g-2 mb-2"><div class="col"><input type="number" class="form-control" id="rAge" placeholder="Umur" required oninput="if(this.value.length>2)this.value=this.value.slice(0,2)"></div><div class="col"><select id="rIns" class="form-select"><option value="Non-BPJS">Umum</option><option value="BPJS">BPJS</option></select></div></div><textarea class="form-control mb-2" id="rHist" placeholder="Riwayat Penyakit"></textarea><input class="form-control mb-2" id="rUser" placeholder="Username Baru" required><input type="password" class="form-control mb-3" id="rPass" placeholder="Password Baru" required><button class="btn btn-success w-100">Daftar Akun Pasien</button></form></div>
        </div>
    </div></div></div></div>

    <!-- EXAM MODAL -->
    <div class="modal fade" id="examModal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog modal-xl"><div class="modal-content"><div class="modal-header bg-success text-white"><h5 class="modal-title">Pemeriksaan</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" id="exId"><div class="row"><div class="col-md-6 border-end"><h6 class="text-success fw-bold">1. Medis</h6><div class="input-group mb-2"><span class="input-group-text">TTV</span><input id="exBP" class="form-control" placeholder="Tensi"><input id="exW" class="form-control" placeholder="Berat (kg)"><input id="exT" class="form-control" placeholder="Suhu (C)"></div><textarea id="exDiag" class="form-control mb-3" rows="2" placeholder="Diagnosa..."></textarea><h6 class="text-success fw-bold">2. Tindakan</h6><div class="card bg-light p-2" style="height:150px;overflow:auto" id="actList"></div></div><div class="col-md-6"><h6 class="text-success fw-bold">3. Resep</h6><div class="card p-0 mb-2" style="height:250px;overflow:auto"><div id="medList" class="list-group list-group-flush"></div></div><textarea id="exNote" class="form-control form-control-sm" placeholder="Catatan Dosis..."></textarea></div></div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-success" onclick="saveExamination()">Simpan</button></div></div></div></div>

    <!-- UTILS MODALS -->
    <div class="modal fade" id="printModal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Cetak</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body d-flex justify-content-center" id="printContent"></div><div class="modal-footer"><button class="btn btn-primary" onclick="window.print()">Print</button></div></div></div></div>
    <div class="modal fade" id="profileModal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Profil</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form onsubmit="saveProfile(event)"><input id="epName" class="form-control mb-2"><input id="epNik" class="form-control mb-2" oninput="if(this.value.length>16)this.value=this.value.slice(0,16)"><input id="epAge" class="form-control mb-2" oninput="if(this.value.length>2)this.value=this.value.slice(0,2)"><select id="epIns" class="form-select mb-3"><option value="Non-BPJS">Umum</option><option value="BPJS">BPJS</option></select><button class="btn btn-primary w-100">Simpan</button></form></div></div></div></div>
    <div class="modal fade" id="historyModal"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-info text-white"><h5 class="modal-title">Rekam Medis</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><h6 id="historyPatientName" class="fw-bold mb-3"></h6><ul class="list-group" id="medicalHistoryList"></ul></div></div></div></div>

    <script>
        // --- KONFIGURASI SUPABASE ---
        const SUPABASE_URL = 'https://vcmunveikzewruxwercx.supabase.co';
        const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InZjbXVudmVpa3pld3J1eHdlcmN4Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgzOTk3MDgsImV4cCI6MjA4Mzk3NTcwOH0.X3izM-TA7jZOqV-1bRm8jtg_zsC1OuGRQf47D_tdSeQ';
        
        const sbClient = window.supabase ? window.supabase.createClient(SUPABASE_URL, SUPABASE_KEY) : null;

        const api = {
            get: async (table) => {
                if (!sbClient) return [];
                const { data, error } = await sbClient.from(table).select('*');
                if (error) { console.error('Fetch error:', error); return []; }
                return data;
            },
            add: async (table, item) => {
                if (!sbClient) return null;
                const { data, error } = await sbClient.from(table).insert([item]).select();
                if (error) { console.error('Insert error:', error); return null; }
                return data[0];
            },
            update: async (table, id, updates) => {
                if (!sbClient) return null;
                const { data, error } = await sbClient.from(table).update(updates).eq('id', id).select();
                if (error) { console.error('Update error:', error); 
                             Swal.fire('Database Error', error.message, 'error');
                             return null; 
                }
                return data[0];
            },
            delete: async (table, id) => {
                if (!sbClient) return;
                const { error } = await sbClient.from(table).delete().eq('id', id);
                if (error) console.error('Delete error:', error);
            }
        };

        let SESS = JSON.parse(sessionStorage.getItem('RS_SESS'));

        // --- AUTH ---
        function checkSession() {
            ['role-admin','role-doctor','role-user'].forEach(c=>document.querySelector('.'+c)?.classList.add('hidden'));
            if (SESS) {
                document.getElementById('btnLogin').classList.add('hidden');
                document.getElementById('userProfile').classList.remove('hidden');
                document.getElementById('uName').innerText = SESS.name;
                document.getElementById('uRole').innerText = SESS.role.toUpperCase();
                
                if (SESS.role==='admin') document.querySelector('.role-admin')?.classList.remove('hidden');
                if (SESS.role==='dokter') document.querySelector('.role-doctor')?.classList.remove('hidden');
                if (SESS.role==='user') {
                    document.querySelector('.role-user')?.classList.remove('hidden');
                    if(document.getElementById('regName')) document.getElementById('regName').value = SESS.name;
                    if(document.getElementById('patientInfoDisplay')) document.getElementById('patientInfoDisplay').innerHTML = `${SESS.name} | ${SESS.age} Th | ${SESS.insurance}`;
                }
                const heroBtn = document.getElementById('heroAuthBtn');
                if (heroBtn) heroBtn.innerText = "Dashboard Saya";
            } else {
                document.getElementById('btnLogin').classList.remove('hidden');
                document.getElementById('userProfile').classList.add('hidden');
                const heroBtn = document.getElementById('heroAuthBtn');
                if (heroBtn) heroBtn.innerText = "Masuk / Daftar";
            }
        }

        async function initApp() {
            checkSession();
            await renderHome();
            if(!window.location.hash) showSection('home');
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('regDate');
            if(dateInput) { dateInput.value = today; dateInput.min = today; }
            setInterval(() => { const el=document.getElementById('clock'); if(el) el.innerText=new Date().toLocaleTimeString(); }, 1000);
            setInterval(checkNotif, 5000);
        }

        async function renderHome() {
            const docs = await api.get('doctors');
            const polis = [...new Set(docs.map(d => d.poli))];
            document.getElementById('homePoliList').innerHTML = polis.map(p => `<div class="col-6 col-md-3"><div class="card p-3 text-center h-100 border-primary"><i class="fas fa-stethoscope fa-2x text-primary mb-2"></i><h6>${p}</h6></div></div>`).join('');
            document.getElementById('publicDoctorList').innerHTML = docs.map(d => `<div class="col-md-3"><div class="card h-100"><div class="card-body text-center"><i class="fas fa-user-md fa-3x text-primary mb-3"></i><h5>${d.name}</h5><small>${d.poli}</small><br><small class="text-muted">${d.schedule}</small></div></div></div>`).join('');
            const opts = '<option disabled selected>Pilih...</option>' + polis.map(p => `<option value="${p}">${p}</option>`).join('');
            if (document.getElementById('regPoli')) document.getElementById('regPoli').innerHTML = opts;
            if (document.getElementById('foPoli')) document.getElementById('foPoli').innerHTML = opts;
        }

        async function handleLogin(e) {
            e.preventDefault();
            const u = document.getElementById('lUser').value, p = document.getElementById('lPass').value;
            if (u === 'admin' && p === '123') {
                SESS = { role: 'admin', name: 'Super Admin', id: 'admin' };
                sessionStorage.setItem('RS_SESS', JSON.stringify(SESS));
                safeHideModal('authModal'); e.target.reset(); checkSession(); showSection('admin'); return;
            }
            const doctors = await api.get('doctors');
            const foundDoc = doctors.find(d => d.username === u && d.password === p);
            if (foundDoc) {
                SESS = { ...foundDoc, role: 'dokter' };
                sessionStorage.setItem('RS_SESS', JSON.stringify(SESS));
                safeHideModal('authModal'); e.target.reset(); checkSession(); showSection('doctor'); return;
            }
            const users = await api.get('users');
            const foundUser = users.find(x => x.username === u && x.password === p);
            if (foundUser) {
                SESS = { ...foundUser, role: 'user' };
                sessionStorage.setItem('RS_SESS', JSON.stringify(SESS));
                safeHideModal('authModal'); e.target.reset(); checkSession(); showSection('user'); return;
            }
            Swal.fire('Gagal', 'Username/Password salah', 'error');
        }

        function handleLogout() { SESS = null; sessionStorage.removeItem('RS_SESS'); checkSession(); showSection('home'); }
        async function handleUserRegister(e) {
            e.preventDefault();
            const nik = document.getElementById('rNik').value;
            if (nik.length !== 16) { Swal.fire('Error', 'NIK harus 16 digit', 'error'); return; }
            const newUser = {
                username: document.getElementById('rUser').value, password: document.getElementById('rPass').value, role: 'user',
                name: document.getElementById('rName').value, nik: nik, age: parseInt(document.getElementById('rAge').value),
                insurance: document.getElementById('rIns').value, history: document.getElementById('rHist').value
            };
            if(await api.add('users', newUser)) { safeHideModal('authModal'); e.target.reset(); Swal.fire('Sukses', 'Akun dibuat, silakan login', 'success'); }
            else Swal.fire('Error', 'Gagal membuat akun', 'error');
        }

        async function genQ(poli) {
            const today = new Date().toISOString().split('T')[0];
            const { count } = await sbClient.from('appointments').select('*', { count: 'exact', head: true }).eq('poli', poli).eq('date', today);
            return poli.charAt(5).toUpperCase() + '-' + String((count || 0) + 1).padStart(3, '0');
        }

        async function regPat(n, p, s, i, ni, a) {
            const q = await genQ(p);
            const saved = await api.add('appointments', {
                user_id: SESS?.id || null, name: n, poli: p, insurance: i, nik: ni, age: a,
                status: 'Menunggu Pemeriksaan', queue_no: q, date: new Date().toISOString().split('T')[0],
                total_cost: 0, real_cost: 0, actions: [], medicine_ids: []
            });
            if (saved) {
                if (s === 'Walk-In') {
                     document.getElementById('printContent').innerHTML=`<div class="receipt-box text-center"><h5>VIOLET HOSPITAL</h5><p>Karcis Antrian</p><hr style="border-top: 2px dashed black"><h1>${q}</h1><p>${p}</p><small>${n}</small><br><small>${i}</small><hr style="border-top: 2px dashed black"><small>Silakan Menunggu</small></div>`;
                     new bootstrap.Modal(document.getElementById('printModal')).show();
                } else { Swal.fire('Sukses', `Antrian: ${q}`, 'success'); nav('user'); }
            }
        }

        async function handleWalkIn(e) { e.preventDefault(); const nik = document.getElementById('foNik').value; if (nik.length !== 16) { Swal.fire('Error', 'NIK 16 digit', 'error'); return; } await regPat(document.getElementById('foName').value, document.getElementById('foPoli').value, 'Walk-In', document.getElementById('foIns').value, nik, document.getElementById('foAge').value); e.target.reset(); }
        async function handleRegistration(e) { e.preventDefault(); await regPat(SESS.name, document.getElementById('regPoli').value, 'Online', SESS.insurance, SESS.nik, SESS.age); document.getElementById('regPoli').value = ''; }

        function nav(p) { document.querySelectorAll('section, .queue-container').forEach(e => e.classList.add('hidden')); const el = document.getElementById('page-' + p) || document.getElementById('section-' + p); if (el) el.classList.remove('hidden'); window.scrollTo(0, 0); if (p === 'admin') { loadCashier(); loadPharma(); loadBeds(); loadReports(); loadMaster(); } if (p === 'doctor') loadDocQueue(); if (p === 'user') loadUserHistory(); }
        function showSection(p) { nav(p); }
        function safeHideModal(id) { const el = document.getElementById(id); if (el) { const m = bootstrap.Modal.getOrCreateInstance(el); if (m) m.hide(); } }
        function showAuthModal() { new bootstrap.Modal(document.getElementById('authModal')).show(); }
        function handleHeroAuth() { const s = SESS; if(s && Object.keys(s).length > 0) { if(s.role === 'admin') showSection('admin'); else if(s.role === 'dokter') showSection('doctor'); else showSection('user'); } else { showAuthModal(); } }

        async function loadUserHistory() {
            if (!SESS) return;
            const { data } = await sbClient.from('appointments').select('*').or(`user_id.eq.${SESS.id},name.eq.${SESS.name}`).order('created_at', { ascending: false });
            document.getElementById('userHistoryList').innerHTML = (data && data.length) ? data.map(x => `<li class="list-group-item d-flex justify-content-between"><span><b>${x.date}</b> ${x.poli} (${x.queue_no})</span><span class="badge bg-${x.status === 'Selesai' ? 'success' : 'warning'}">${x.status}</span></li>`).join('') : '<li class="list-group-item text-center text-muted">Belum ada riwayat.</li>';
        }

        async function loadDocQueue() {
            if (!SESS || SESS.role !== 'dokter') return;
            document.getElementById('docNameDisplay').innerText = SESS.name; document.getElementById('docPoliDisplay').innerText = SESS.poli;
            const { data } = await sbClient.from('appointments').select('*').eq('poli', SESS.poli).in('status', ['Menunggu Pemeriksaan', 'Dipanggil', 'Periksa']).order('created_at', { ascending: true });
            const tb = document.getElementById('doctorTableBody'); tb.innerHTML = '';
            if (!data || data.length === 0) { document.getElementById('doctorEmptyState').classList.remove('hidden'); } 
            else { document.getElementById('doctorEmptyState').classList.add('hidden'); data.forEach(x => {
                    let btn = '';
                    if (x.status === 'Menunggu Pemeriksaan') btn = `<button onclick="setSt(${x.id},'Dipanggil')" class="btn btn-sm btn-primary">Panggil</button>`;
                    else if (x.status === 'Dipanggil') btn = `<button onclick="setSt(${x.id},'Periksa')" class="btn btn-sm btn-success">Periksa</button>`;
                    else btn = `<button onclick="openExam(${x.id})" class="btn btn-sm btn-success">Lanjut Periksa</button>`;
                    tb.innerHTML += `<tr class="${x.status === 'Dipanggil' ? 'table-warning' : ''}"><td>${x.queue_no}</td><td><b>${x.name}</b><br><small>${x.age}Th | ${x.insurance}</small></td><td>${x.status}</td><td>${btn}</td></tr>`;
            }); }
        }

        async function setSt(id, s) { await api.update('appointments', id, { status: s }); loadDocQueue(); }
        let curP = null;
        async function openExam(id) {
            curP = id; await setSt(id, 'Periksa');
            document.getElementById('actList').innerHTML = ['Konsultasi (50k)', 'EKG (75k)', 'Nebulizer (40k)'].map(a => { let [n, p] = a.split(' ('); return `<div class="form-check"><input class="form-check-input ac" type="checkbox" value="${n}" data-p="${parseInt(p) * 1000}"><label>${a}</label></div>`; }).join('');
            const meds = await api.get('medicines');
            document.getElementById('medList').innerHTML = meds.filter(m => m.stock > 0).map(m => `<label class="list-group-item"><input class="form-check-input me-2 mc" type="checkbox" value="${m.id}" data-p="${m.price}" data-n="${m.name}"> ${m.name} (${m.stock}) - ${m.price}</label>`).join('');
            new bootstrap.Modal(document.getElementById('examModal')).show();
        }

        async function saveExamination() {
            try {
                // 1. Validasi Input
                const diagnosis = document.getElementById('exDiag').value;
                if (!diagnosis) { Swal.fire('Peringatan', 'Diagnosa wajib diisi!', 'warning'); return; }

                // 2. Validasi Stok Obat
                let meds = await api.get('medicines');
                let insufficient = false;
                let selectedMedIds = [];
                
                document.querySelectorAll('.mc:checked').forEach(c => { 
                    let m = meds.find(x => x.id == c.value); 
                    if (!m || m.stock <= 0) insufficient = true; 
                    selectedMedIds.push(m.id); 
                });

                if (insufficient) { Swal.fire('Stok Habis', 'Salah satu obat yang dipilih habis. Silakan refresh halaman.', 'error'); return; }

                // 3. Ambil Data Pasien (Untuk cek asuransi)
                const { data: patients, error: errPat } = await sbClient.from('appointments').select('*').eq('id', curP);
                if (errPat || !patients || patients.length === 0) { throw new Error("Gagal memuat data pasien."); }
                const patient = patients[0];

                // 4. Hitung Biaya & Update Stok Realtime
                let cost = 0, items = [];
                
                // Hitung Tindakan
                document.querySelectorAll('.ac:checked').forEach(c => { 
                    cost += parseInt(c.dataset.p); 
                    items.push(c.value); 
                });

                // Hitung Obat & Kurangi Stok
                for (const c of document.querySelectorAll('.mc:checked')) {
                    cost += parseInt(c.dataset.p); 
                    items.push(c.dataset.n); 
                    
                    // Update stok di database
                    let m = meds.find(x => x.id == c.value);
                    if(m) await api.update('medicines', m.id, { stock: m.stock - 1 });
                }

                // 5. Update Status Pasien -> Bayar
                // FIX: Menangani kolom diagnosis yang hilang dengan menyimpannya di JSONB 'ttv'
                const updates = { 
                    ttv: { 
                        bp: document.getElementById('exBP').value, 
                        w: document.getElementById('exW').value, 
                        t: document.getElementById('exT').value,
                        diag: diagnosis // Simpan diagnosa disini
                    }, 
                    // diagnosis: diagnosis, // Dihapus untuk mencegah error
                    medicine_text: document.getElementById('exNote').value, 
                    actions: items, 
                    medicine_ids: selectedMedIds, 
                    real_cost: cost, 
                    total_cost: patient.insurance === 'BPJS' ? 0 : cost, 
                    status: 'Bayar' 
                };

                const result = await api.update('appointments', curP, updates);
                if(!result) throw new Error("Gagal menyimpan data ke server.");

                // 6. Selesai
                safeHideModal('examModal'); 
                loadDocQueue();
                Swal.fire('Berhasil', 'Pemeriksaan selesai. Data dikirim ke Kasir.', 'success');

            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'Terjadi kesalahan: ' + e.message, 'error');
            }
        }

        async function loadCashier() {
            const search = document.getElementById('searchCashier')?.value.toLowerCase() || '';
            const { data } = await sbClient.from('appointments').select('*').eq('status', 'Bayar').ilike('name', `%${search}%`);
            document.getElementById('cashierTableBody').innerHTML = (data && data.length) ? data.map(x => `<tr><td>${x.queue_no}</td><td>${x.name}<br><small>${x.insurance}</small></td><td>${x.insurance}</td><td>Rp ${x.total_cost.toLocaleString()}</td><td><button onclick="printStruk(${x.id})" class="btn btn-sm btn-info text-white"><i class="fas fa-print"></i></button> <button onclick="confirmPay(${x.id})" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button></td></tr>`).join('') : '';
            if(!data || !data.length) document.getElementById('cashierEmptyState').classList.remove('d-none'); else document.getElementById('cashierEmptyState').classList.add('d-none');
        }
        async function printStruk(id) {
            const { data: [p] } = await sbClient.from('appointments').select('*').eq('id', id);
            
            // Handle Actions (JSONB array to string)
            let itemsHtml = '-';
            if (p.actions && Array.isArray(p.actions)) {
                itemsHtml = p.actions.map(i => `<div>• ${i}</div>`).join('');
            }
            
            let noteHtml = p.medicine_text ? `<br>Note: ${p.medicine_text}` : '';
            // Get diagnosis from ttv jsonb
            let diagHtml = (p.ttv && p.ttv.diag) ? `<div class="mb-2"><strong>Diagnosa:</strong> ${p.ttv.diag}</div>` : '';

            document.getElementById('printContent').innerHTML = `
                <div class="receipt-box">
                    <div class="text-center mb-3"><h5 class="fw-bold mb-0">VIOLET HOSPITAL</h5><small>Jl. Sehat</small></div>
                    <hr style="border-top:1px dashed #000">
                    <div class="d-flex justify-content-between"><span>${p.date}</span><span>${p.queue_no}</span></div>
                    <div>${p.name} (${p.insurance})</div>
                    <hr style="border-top:1px dashed #000">
                    ${diagHtml}
                    <div class="small">${itemsHtml} ${noteHtml}</div>
                    <hr style="border-top:1px dashed #000">
                    <div class="d-flex justify-content-between fw-bold"><span>TOTAL</span><span>Rp ${p.total_cost.toLocaleString()}</span></div>
                </div>`;
            new bootstrap.Modal(document.getElementById('printModal')).show();
        }
        async function confirmPay(id) { await api.update('appointments', id, { status: 'Selesai' }); loadCashier(); }

        async function loadPharma() { const meds = await api.get('medicines'); document.getElementById('pharmacyTableBody').innerHTML = meds.map(m => `<tr><td>${m.name}</td><td>${m.price}</td><td>${m.stock}</td><td>${m.status}</td><td><button onclick="delItem('medicines', ${m.id})" class="btn btn-sm btn-danger">&times;</button></td></tr>`).join(''); }
        async function addMedicine(e) { e.preventDefault(); await api.add('medicines', { name: document.getElementById('newMedName').value, price: parseInt(document.getElementById('newMedPrice').value), stock: parseInt(document.getElementById('newMedStock').value), status: document.getElementById('newMedStatus').value }); loadPharma(); e.target.reset(); }
        async function delItem(table, id) { if(confirm('Hapus?')) { await api.delete(table, id); if(table==='doctors')loadMaster(); if(table==='medicines')loadPharma(); } }

        async function loadBeds() { const beds = await api.get('beds'); document.getElementById('bedListContainer').innerHTML = beds.map(b => { let st = b.status==='available'?'bed-available':(b.status==='occupied'?'bed-occupied':'bed-cleaning'); return `<div class="col-6 col-md-3"><div class="bed-card ${st} p-3" onclick="togBed(${b.id}, '${b.status}')"><i class="fas fa-bed fs-2 mb-2"></i><h5>${b.name}</h5><small>${b.class}</small><br><b>${b.status}</b><br><small>${b.patient||''}</small></div></div>`; }).join(''); }
        async function togBed(id, status) {
            if (status === 'available') Swal.fire({title:'Check-In', input:'text'}).then(async r => { if(r.value) { await api.update('beds', id, { status: 'occupied', patient: r.value }); loadBeds(); }});
            else if (status === 'occupied') Swal.fire({title:'Check-Out?', showCancelButton:true}).then(async r => { if(r.isConfirmed) { await api.update('beds', id, { status: 'cleaning', patient: null }); loadBeds(); }});
            else { await api.update('beds', id, { status: 'available' }); loadBeds(); }
        }

        async function loadReports() {
            const { data } = await sbClient.from('appointments').select('*').eq('status', 'Selesai');
            if(!data) return;
            document.getElementById('reportIncome').innerText = data.reduce((a,b)=>a+(b.insurance!=='BPJS'?b.total_cost:0),0).toLocaleString();
            document.getElementById('reportBPJS').innerText = data.reduce((a,b)=>a+(b.insurance==='BPJS'?b.real_cost:0),0).toLocaleString();
            document.getElementById('reportPatients').innerText = data.length;
            document.getElementById('reportTableBody').innerHTML = data.slice(-10).reverse().map(x => `<tr><td>${x.date}</td><td>${x.name}</td><td>${x.poli}</td><td>${x.insurance}</td><td>${x.total_cost}</td></tr>`).join('');
            // Charts (Simplified)
            const polis=[...new Set(data.map(x=>x.poli))], pD=polis.map(p=>data.filter(x=>x.poli===p).length);
            new Chart(document.getElementById('poliChart'),{type:'bar',data:{labels:polis,datasets:[{label:'Pasien',data:pD,backgroundColor:'#6f42c1'}]}});
            const iD=[data.filter(x=>x.insurance==='BPJS').length, data.filter(x=>x.insurance!=='BPJS').length];
            new Chart(document.getElementById('insuranceChart'),{type:'doughnut',data:{labels:['BPJS','Umum'],datasets:[{data:iD,backgroundColor:['#198754','#ffc107']}]}});
        }
        
        async function loadMaster() { const docs = await api.get('doctors'); document.getElementById('masterDoctorBody').innerHTML = docs.map(d => `<tr><td>${d.name}</td><td>${d.poli}</td><td>${d.schedule}</td><td><button onclick="delItem('doctors',${d.id})" class="btn btn-sm btn-danger">&times;</button></td></tr>`).join(''); }
        async function addDoctor(e) { 
            e.preventDefault(); 
            await api.add('doctors', { 
                name: document.getElementById('newDocName').value, 
                poli: document.getElementById('newDocPoli').value, 
                schedule: document.getElementById('newDocSchedule').value, 
                username: document.getElementById('newDocUser').value, 
                password: document.getElementById('newDocPass').value 
            }); 
            loadMaster(); 
            e.target.reset(); // Reset form setelah simpan
        }
        
        async function tvRun() {
            if(!sbClient) return; document.getElementById('clock').innerText = new Date().toLocaleTimeString();
            const { data } = await sbClient.from('appointments').select('*').in('status', ['Menunggu Pemeriksaan', 'Dipanggil', 'Periksa']);
            if(!data) return;
            const polis = [...new Set(data.map(x => x.poli))];
            document.getElementById('queueListContainer').innerHTML = polis.length ? polis.map(p => {
                const c = data.find(x => x.poli === p && x.status !== 'Menunggu Pemeriksaan');
                const w = data.filter(x => x.poli === p && x.status === 'Menunggu Pemeriksaan').slice(0, 3);
                return `<div class="col-md-4"><div class="queue-box"><h4 class="text-info border-bottom pb-2 mb-3">${p}</h4><div class="mb-4"><div class="q-num ${c && c.status === 'Dipanggil' ? 'q-blink' : ''}">${c ? c.queue_no : '-'}</div><div class="mt-2 text-white">${c ? c.name : '...'}</div></div><div class="text-start border-top pt-2 text-muted small">${w.map(x => `<div>${x.queue_no} - ${x.name}</div>`).join('')}</div></div></div>`;
            }).join('') : '<div class="text-center text-muted mt-5"><h1>TIDAK ADA ANTRIAN</h1></div>';
        }
        function closeQueueTV() { clearInterval(tvInt); document.getElementById('page-queue').classList.add('hidden'); document.querySelector('nav').classList.remove('hidden'); showSection('home'); }
        
        async function checkNotif() { if(SESS&&SESS.role==='user'){ const {data}=await sbClient.from('appointments').select('*').eq('user_id',SESS.id).eq('status','Dipanggil'); if(data&&data.length){ if(lastStat!=='Dipanggil')Swal.fire('PANGGILAN',`Masuk ke ${data[0].poli}`,'info'); lastStat='Dipanggil'; } else lastStat=null; }}

        // Start
        if(window.supabase) initApp();
        else alert("Supabase library not loaded or Config missing!");

    </script>
</body>
</html>