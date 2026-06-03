<?php
session_start();
if (!isset($_SESSION['login'])) { header('Location: login.php'); exit; }

$page_title = 'Materi';
$base_path  = '../';
require_once '../includes/header.php';

$semua_materi = [
  ['kategori'=>'Matematika',     'icon'=>'📐', 'judul'=>'Persamaan Kuadrat',     'deskripsi'=>'Akar-akar persamaan kuadrat, diskriminan, dan penerapannya.', 'link'=>'persamaan_kuadrat.php'],
  ['kategori'=>'Matematika',     'icon'=>'📊', 'judul'=>'Statistika Dasar',      'deskripsi'=>'Mean, median, modus, varians, dan ukuran penyebaran data.',   'link'=>'statistika_dasar.php'],
  ['kategori'=>'Bahasa Indonesia','icon'=>'📖','judul'=>'Membaca Pemahaman',     'deskripsi'=>'Strategi memahami teks bacaan dengan cepat dan tepat.',        'link'=>'membaca_pemahaman.php'],
  ['kategori'=>'Bahasa Indonesia','icon'=>'🧩','judul'=>'Penalaran Verbal',      'deskripsi'=>'Sinonim, antonim, analogi kata, dan penalaran wacana.',        'link'=>'penalaran_verbal.php'],
  ['kategori'=>'Bahasa Inggris', 'icon'=>'🌍', 'judul'=>'Reading Comprehension', 'deskripsi'=>'Teknik menjawab soal bacaan bahasa Inggris UTBK.',             'link'=>'reading_comprehension.php'],
  ['kategori'=>'Penalaran Umum', 'icon'=>'🧠', 'judul'=>'Logika Deduktif',       'deskripsi'=>'Silogisme, penalaran kondisional, dan pemecahan masalah.',     'link'=>'logika_deduktif.php'],
];

// Filter aktif
$filter = $_GET['kat'] ?? 'Semua';
$kategori_list = ['Semua','Matematika','Bahasa Indonesia','Bahasa Inggris','Penalaran Umum'];
$filtered = $filter === 'Semua' ? $semua_materi : array_filter($semua_materi, fn($m) => $m['kategori'] === $filter);
?>

<main>
  <div class="page-header animate__animated animate__fadeIn">
    <h1>Materi UTBK</h1>
    <p>Modul PDF lengkap untuk semua mata pelajaran yang diujikan di UTBK</p>
  </div>

  <!-- Filter kategori -->
  <div class="forum-category" style="padding:0 24px;margin-bottom:0">
    <?php foreach ($kategori_list as $kat): ?>
    <a href="materi.php?kat=<?= urlencode($kat) ?>"
       class="forum-tag <?= $filter===$kat?'active':'' ?>">
       <?= $kat === 'Semua' ? '🗂 Semua' : $kat ?>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="section">
    <div class="card-grid">
      <?php $i=0; foreach ($filtered as $m): $i++; ?>
      <div class="card animate__animated animate__fadeInUp" style="animation-delay:<?= $i*0.08 ?>s">
        <div style="font-size:36px;margin-bottom:10px"><?= $m['icon'] ?></div>
        <span class="card-badge"><?= htmlspecialchars($m['kategori']) ?></span>
        <h3 style="margin-top:8px"><?= htmlspecialchars($m['judul']) ?></h3>
        <p><?= htmlspecialchars($m['deskripsi']) ?></p>
        <a href="<?= $m['link'] ?>" class="btn-blue" style="margin-top:auto">Buka Materi →</a>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($filtered)): ?>
      <p style="color:#888;text-align:center;padding:40px">Tidak ada materi untuk kategori ini.</p>
    <?php endif; ?>
  </div>
</main>

<?php require_once '../includes/footer.php'; ?>
