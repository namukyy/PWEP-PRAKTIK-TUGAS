<?php
session_start();
if (!isset($_SESSION['login'])) { header('Location: login.php'); exit; }
$page_title = 'Beranda';
$base_path  = '../';
require_once '../includes/header.php';
$nama = $_SESSION['nama_user'] ?? 'Siswa';
?>

<main>

<!-- ── HERO ── -->
<section class="hero animate__animated animate__fadeIn">

  <h1 class="hero-title">Digital Education<br><span style="color:var(--biru)">for Everyone</span></h1>
  <p class="hero-subtitle">
    Halo, <strong><?= htmlspecialchars($nama) ?></strong>! Persiapan UTBK lebih mudah, 
    terjangkau, dan bisa diakses kapan saja. Raih skor impianmu bersama DigEdu.
  </p>
  <div class="hero-buttons animate__animated animate__fadeInUp" style="animation-delay:.3s">
    <a href="materi.php"  class="btn-primary">📚 Mulai Belajar</a>
    <a href="tryout.php"  class="btn-outline">📝 Ikut Tryout</a>
    <a href="forum.php"   class="btn-outline">💬 Forum Diskusi</a>
  </div>
</section>

<!-- ── FITUR UTAMA ── -->
<section class="features">
  <div class="feature-item animate__animated animate__fadeInUp" style="animation-delay:.1s">
    <div class="feature-icon">📚</div>
    <h3>Materi Lengkap</h3>
    <p>6 mata pelajaran UTBK tersedia dalam bentuk modul PDF yang bisa dibuka langsung dari browser.</p>
    <a href="materi.php" class="btn-blue" style="margin-top:12px;display:inline-block">Lihat Materi →</a>
  </div>
  <div class="feature-item animate__animated animate__fadeInUp" style="animation-delay:.2s">
    <div class="feature-icon">📝</div>
    <h3>Tryout Online</h3>
    <p>4 paket tryout soal pilihan ganda dengan timer otomatis. Nilai langsung keluar setelah selesai!</p>
    <a href="tryout.php" class="btn-blue" style="margin-top:12px;display:inline-block">Mulai Tryout →</a>
  </div>
  <div class="feature-item animate__animated animate__fadeInUp" style="animation-delay:.3s">
    <div class="feature-icon">💬</div>
    <h3>Forum Diskusi</h3>
    <p>Tanya jawab, berbagi tips belajar, dan berdiskusi dengan sesama siswa UTBK.</p>
    <a href="forum.php" class="btn-blue" style="margin-top:12px;display:inline-block">Buka Forum →</a>
  </div>
  <div class="feature-item animate__animated animate__fadeInUp" style="animation-delay:.4s">
    <div class="feature-icon">📈</div>
    <h3>Lacak Progress</h3>
    <p>Dashboard personal menampilkan statistik belajar, riwayat tryout, dan progress per mata pelajaran.</p>
    <a href="dashboard.php" class="btn-blue" style="margin-top:12px;display:inline-block">Dashboard →</a>
  </div>
</section>

<!-- ── STATISTIK PLATFORM ── -->
<section class="section" style="background:var(--biru-tua);color:#fff;border-radius:16px;margin:0 24px 32px;padding:40px 32px">
  <h2 style="text-align:center;font-size:22px;margin-bottom:28px;color:#fff">Dipercaya oleh ribuan siswa</h2>
  <div class="stats-grid" style="--card-bg:#fff2">
    <div class="stat-card" style="background:rgba(255,255,255,.12);color:#fff;border:none">
      <div class="stat-num" style="color:#fff">4</div>
      <div class="stat-label" style="color:rgba(255,255,255,.8)">Paket Tryout</div>
    </div>
    <div class="stat-card" style="background:rgba(255,255,255,.12);color:#fff;border:none">
      <div class="stat-num" style="color:#fff">6</div>
      <div class="stat-label" style="color:rgba(255,255,255,.8)">Materi Tersedia</div>
    </div>
    <div class="stat-card" style="background:rgba(255,255,255,.12);color:#fff;border:none">
      <div class="stat-num" style="color:#fff">20+</div>
      <div class="stat-label" style="color:rgba(255,255,255,.8)">Modul PDF</div>
    </div>
    <div class="stat-card" style="background:rgba(255,255,255,.12);color:#fff;border:none">
      <div class="stat-num" style="color:#fff">100%</div>
      <div class="stat-label" style="color:rgba(255,255,255,.8)">Gratis</div>
    </div>
  </div>
</section>

<!-- ── CTA BAWAH ── -->
<section class="section" style="text-align:center;padding:32px 24px">
  <h2 style="font-size:20px;color:var(--biru-tua);margin-bottom:10px">Siap mulai persiapan UTBK? 🚀</h2>
  <p style="color:var(--abu-teks);margin-bottom:20px">Pilih paket tryout dan kerjakan sekarang juga!</p>
  <a href="tryout.php" class="btn-primary">Mulai Tryout Sekarang</a>
</section>

</main>

<?php require_once '../includes/footer.php'; ?>
