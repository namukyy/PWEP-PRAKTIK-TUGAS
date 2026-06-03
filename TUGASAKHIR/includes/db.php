<?php
// ============================================================
//  DigEdu – Koneksi Database
//  Sesuaikan host, user, password jika berbeda
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'digedu');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:40px;color:#842029;background:#fff0f0;border:1px solid #f5c6cb;border-radius:8px;margin:40px auto;max-width:500px;">
        <strong>❌ Koneksi Database Gagal</strong><br><br>
        ' . htmlspecialchars($conn->connect_error) . '<br><br>
        <small>Pastikan MySQL berjalan dan file <code>includes/db.php</code> sudah dikonfigurasi dengan benar.</small>
    </div>');
}
