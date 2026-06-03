<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$nama_user = $_SESSION['nama_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($page_title) ? htmlspecialchars($page_title).' – DigEdu' : 'DigEdu – Platform Belajar UTBK' ?></title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Custom CSS (tetap dipakai bersamaan) -->
  <link rel="stylesheet" href="<?= $base_path ?? '' ?>assets/css/style.css" />

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Tailwind config: custom color palette matching DigEdu
    tailwind.config = {
      corePlugins: { preflight: false }, // jangan reset CSS global
      theme: {
        extend: {
          colors: {
            brand: {
              blue:    '#2563c7',
              dark:    '#1a3a6b',
              light:   '#e8f0fe',
              green:   '#28a745',
              gray:    '#6b7280',
              border:  '#e5e7eb',
            }
          }
        }
      }
    }
  </script>
</head>
<body>

<nav class="navbar">
  <a href="<?= $base_path ?? '' ?>pages/beranda.php" class="brand">
    DigEdu <span>UTBK</span>
  </a>
  <div class="nav-links">
    <a href="<?= $base_path ?? '' ?>pages/beranda.php"   class="<?= $current_page==='beranda'   ? 'active':'' ?>">Beranda</a>
    <a href="<?= $base_path ?? '' ?>pages/dashboard.php" class="<?= $current_page==='dashboard' ? 'active':'' ?>">Dashboard</a>
    <a href="<?= $base_path ?? '' ?>pages/materi.php"    class="<?= $current_page==='materi'    ? 'active':'' ?>">Materi</a>
    <a href="<?= $base_path ?? '' ?>pages/tryout.php"    class="<?= $current_page==='tryout'    ? 'active':'' ?>">Tryout</a>
    <a href="<?= $base_path ?? '' ?>pages/forum.php"     class="<?= $current_page==='forum'     ? 'active':'' ?>">Forum</a>
    <?php if ($nama_user): ?>
      <span class="nav-user">👋 <?= htmlspecialchars($nama_user) ?></span>
    <?php endif; ?>
    <a href="<?= $base_path ?? '' ?>pages/logout.php" class="btn-logout">Logout</a>
  </div>
</nav>
