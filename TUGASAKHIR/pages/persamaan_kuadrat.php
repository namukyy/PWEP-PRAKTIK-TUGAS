<?php
session_start();
if (!isset($_SESSION["login"])) { header("Location: login.php"); exit; }
$page_title = 'Persamaan Kuadrat';
$base_path  = '../';
require_once '../includes/header.php';

$modul_list = [
  ['judul' => 'Modul 1', 'topik' => 'Pengenalan Persamaan Kuadrat',   'link' => 'https://drive.google.com/file/d/1YcB6IJHgfiCxUwvv31CBgnBR8qX85G2s/view?usp=drive_link'],
  ['judul' => 'Modul 2', 'topik' => 'Diskriminan dan Akar Persamaan', 'link' => 'https://drive.google.com/file/d/1b4nm7RWz2fAmwukQ233fNPyQNVupbek4/view?usp=drive_link'],
  ['judul' => 'Modul 3', 'topik' => 'Pemfaktoran Persamaan Kuadrat',  'link' => 'https://drive.google.com/file/d/1O4oKUxlmoUAA1euGPAQr62zbkn8G1b-p/view?usp=sharing'],
  ['judul' => 'Modul 4', 'topik' => 'Soal dan Pembahasan',            'link' => 'https://drive.google.com/file/d/1TTd1zmKQP8wA11PbebItzxavB6q49far/view?usp=sharing'],
  ['judul' => 'Modul 5', 'topik' => 'Latihan Soal UTBK',              'link' => 'https://drive.google.com/file/d/1-vb4C1ycSxzRlgA9Kx5r5SFok0nh-TQj/view?usp=sharing'],
];
?>

<main>
  <div class="materi-header">
    <h1>📘 Materi Persamaan Kuadrat</h1>
    <p>Materi ini membahas bentuk umum persamaan kuadrat, diskriminan, akar-akar persamaan, pemfaktoran, serta latihan soal untuk persiapan UTBK.</p>
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
