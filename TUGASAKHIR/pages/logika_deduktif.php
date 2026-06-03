<?php
session_start();
if (!isset($_SESSION["login"])) { header("Location: login.php"); exit; }
$page_title = 'Logika Deduktif';
$base_path  = '../';
require_once '../includes/header.php';

$modul_list = [
  ['judul' => 'Modul 1', 'topik' => 'Pengenalan Logika Deduktif',   'link' => 'https://drive.google.com/file/d/12IJOX57VvVhR5Vnl474GGjkLAFXRhRhB/view?usp=sharing'],
  ['judul' => 'Modul 2', 'topik' => 'Silogisme Kategoris',          'link' => 'https://drive.google.com/file/d/134DepMjEDd6WQ5vVIesenrVnTHkht5vC/view?usp=sharing'],
  ['judul' => 'Modul 3', 'topik' => 'Penalaran Kondisional',        'link' => 'https://drive.google.com/file/d/15ug3nXzEfWndPyiaQRgZFp7N5ohFHka0/view?usp=drive_link'],
  ['judul' => 'Modul 4', 'topik' => 'Pemecahan Masalah Logika',     'link' => 'https://drive.google.com/file/d/15ug3nXzEfWndPyiaQRgZFp7N5ohFHka0/view?usp=drive_link'],
  ['judul' => 'Modul 5', 'topik' => 'Latihan Soal Penalaran UTBK',  'link' => 'https://drive.google.com/file/d/1yxyrXf-n8NiDaATsuQSml5NEpwbXYp4S/view?usp=sharing'],
];
?>
<main>
  <div class="materi-header">
    <h1>🧠 Materi Logika Deduktif</h1>
    <p>Materi ini membahas silogisme, penalaran kondisional, dan pemecahan masalah logika yang sering muncul pada tes Penalaran Umum UTBK.</p>
  </div>
  <div class="modul-grid">
    <?php
 foreach ($modul_list as $m): ?>
    <div class="modul-card">
      <h3>📖 <?= $m['judul'] ?></h3>
      <p><?= htmlspecialchars($m['topik']) ?></p>
      <a href="<?= $m['link'] ?>" target="_blank" class="btn-blue">Buka Materi</a>
    </div>
    <?php
 endforeach; ?>
  </div>
</main>
<?php
 require_once '../includes/footer.php'; ?>
