<?php
session_start();
if (!isset($_SESSION["login"])) { header("Location: login.php"); exit; }
$page_title = 'Membaca Pemahaman';
$base_path  = '../';
require_once '../includes/header.php';

$modul_list = [
  ['judul' => 'Modul 1', 'topik' => 'Ide Pokok Paragraf',          'link' => 'https://drive.google.com/file/d/1No-C65nnzfYrBv7BtoE2OzQEbebeTyhv/view?usp=sharing'],
  ['judul' => 'Modul 2', 'topik' => 'Kalimat Utama dan Pendukung', 'link' => 'https://drive.google.com/file/d/1rNCF2l4EP8zLdT90-YuHy3XsAZkaupbe/view?usp=drive_link'],
  ['judul' => 'Modul 3', 'topik' => 'Menarik Kesimpulan',          'link' => 'https://drive.google.com/file/d/1tT8z75ObJ8I3rzOj37n4QFGqxCB_qjcF/view?usp=drive_link'],
  ['judul' => 'Modul 4', 'topik' => 'Strategi Membaca Cepat',      'link' => 'https://drive.google.com/file/d/1kmDbgaO93Ep3gkI9xN3WSbEt47eVGDKq/view?usp=drive_link'],
  ['judul' => 'Modul 5', 'topik' => 'Latihan Soal Literasi',       'link' => 'https://drive.google.com/file/d/1V0Z3shzbpPq8_EDsUH8QVcVCqc01TdEG/view?usp=drive_link'],
];
?>

<main>
  <div class="materi-header">
    <h1>📖 Materi Membaca Pemahaman</h1>
    <p>Materi ini membantu siswa memahami isi bacaan, menemukan ide pokok, menarik kesimpulan, dan menjawab soal literasi dengan tepat.</p>
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
