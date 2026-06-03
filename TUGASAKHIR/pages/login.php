<?php
session_start();
require_once '../includes/db.php';

if (isset($_SESSION['login'])) {
   header("Location: beranda.php");
   exit;
}

$error   = '';
$success = '';
$mode    = $_POST['mode'] ?? ($_GET['mode'] ?? 'login');

// ── REGISTER ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'register') {
    $nama     = trim($_POST['nama']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $konfirm  = trim($_POST['konfirm']  ?? '');

    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirm) {
        $error = 'Password dan konfirmasi tidak cocok.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            // Simpan sebagai MD5 (konsisten dengan seed data)
            $hashed = md5($password);
            $ins    = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            $ins->bind_param('sss', $nama, $email, $hashed);
            if ($ins->execute()) {
                $success = '✅ Akun berhasil dibuat! Silakan login.';
                $mode    = 'login';
            } else {
                $error = 'Gagal mendaftar. Coba lagi.';
            }
        }
    }
}

// ── LOGIN ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'login') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Email dan password tidak boleh kosong.';
    } else {
        $stmt = $conn->prepare("SELECT id, nama, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id, $nama, $hash);
        $found = $stmt->fetch();
        $stmt->close();

        if ($found) {
            // Support MD5 (seed data) dan bcrypt (akun baru)
            $valid = false;
            if (strlen($hash) === 32) {
                $valid = ($hash === md5($password));
            } else {
                $valid = password_verify($password, $hash);
            }
            if ($valid) {
                $_SESSION['login']     = true;
                $_SESSION['user_id']   = $id;
                $_SESSION['nama_user'] = $nama;
                header("Location: beranda.php");
                exit;
            }
        }
        $error = 'Email atau password salah.';
    }
}

$show_register = ($mode === 'register' && !$success);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $show_register ? 'Daftar' : 'Login' ?> – DigEdu</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>
<body>

<nav class="navbar">
  <a href="../index.php" class="brand">DigEdu <span>UTBK</span></a>
</nav>

<main>
  <div class="login-wrap">
    <div class="login-card animate__animated animate__fadeInUp">

      <?php if ($show_register): ?>
        <h2>Buat Akun Baru</h2>
        <p class="subtitle">Daftar gratis dan mulai belajar UTBK</p>

        <?php if ($error): ?>
          <div class="alert-error animate__animated animate__shakeX"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
          <input type="hidden" name="mode" value="register" />
          <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" placeholder="Nama kamu"
                   value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required />
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="email@kamu.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required />
          </div>
          <div class="form-group">
            <label for="konfirm">Konfirmasi Password</label>
            <input type="password" id="konfirm" name="konfirm" placeholder="Ulangi password" required />
          </div>
          <button type="submit" class="btn-primary w-full">Daftar Sekarang</button>
        </form>
        <p class="login-footer">
          Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </p>

      <?php else: ?>
        <h2>Masuk ke DigEdu</h2>
        <p class="subtitle">Gunakan akun kamu untuk melanjutkan belajar</p>

        <?php if ($error): ?>
          <div class="alert-error animate__animated animate__shakeX"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert-success animate__animated animate__fadeIn"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
          <input type="hidden" name="mode" value="login" />
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="siswa@digedu.id"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required />
          </div>
          <button type="submit" class="btn-primary w-full">Masuk</button>
        </form>

        <div class="demo-hint">
          <span>🔑 Demo:</span> <code>siswa@digedu.id</code> / <code>password123</code>
        </div>

        <p class="login-footer">
          Belum punya akun? <a href="login.php?mode=register">Daftar sekarang</a>
        </p>
      <?php endif; ?>

    </div>
  </div>
</main>

<footer>
  <p>© 2025 Digital Education for Everyone · Mendukung SDG 4: Pendidikan Berkualitas</p>
</footer>
</body>
</html>
