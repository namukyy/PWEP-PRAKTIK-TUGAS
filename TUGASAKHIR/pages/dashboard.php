<?php
session_start();
if (!isset($_SESSION['login'])) { header('Location: login.php'); exit; }

$page_title = 'Dashboard';
$base_path  = '../';
require_once '../includes/db.php';
require_once '../includes/header.php';

$user_id    = $_SESSION['user_id']   ?? 0;
$nama_siswa = $_SESSION['nama_user'] ?? 'Siswa';

// ── Statistik dari DB ──────────────────────────────────────
$res_stat     = $conn->query("SELECT COUNT(*) AS total, AVG(nilai) AS avg_nilai FROM tryout_results WHERE user_id = $user_id");
$row_stat     = $res_stat->fetch_assoc();
$total_tryout = (int)($row_stat['total'] ?? 0);
$avg_nilai    = $total_tryout > 0 ? round($row_stat['avg_nilai']) : 0;

// Total thread forum oleh user
$res_forum   = $conn->query("SELECT COUNT(*) AS c FROM forum_threads WHERE user_id = $user_id");
$total_forum = (int)($res_forum->fetch_assoc()['c'] ?? 0);

// ── Progress per kategori ──────────────────────────────────
$progress_map = [
  'Matematika'       => ['jumlah'=>0,'total_nilai'=>0],
  'Bahasa Indonesia' => ['jumlah'=>0,'total_nilai'=>0],
  'Bahasa Inggris'   => ['jumlah'=>0,'total_nilai'=>0],
  'Penalaran Umum'   => ['jumlah'=>0,'total_nilai'=>0],
];
$res_prog = $conn->query("SELECT paket, nilai FROM tryout_results WHERE user_id = $user_id");
while ($rp = $res_prog->fetch_assoc()) {
    foreach ($progress_map as $kat => &$p) {
        if (stripos($rp['paket'], $kat) !== false) {
            $p['jumlah']++;
            $p['total_nilai'] += $rp['nilai'];
        }
    }
}
$progress = [];
foreach ($progress_map as $kat => $p) {
    $persen = $p['jumlah'] > 0 ? min(100, round($p['total_nilai'] / $p['jumlah'])) : 0;
    $color  = $persen >= 80 ? '#28a745' : ($persen >= 60 ? '#f59e0b' : 'var(--biru)');
    $progress[] = ['mata'=>$kat, 'persen'=>$persen, 'color'=>$color];
}

// ── Riwayat tryout terbaru ─────────────────────────────────
$res_riwayat = $conn->query("SELECT paket, nilai, benar, total_soal, dikerjakan FROM tryout_results WHERE user_id = $user_id ORDER BY dikerjakan DESC LIMIT 5");
$riwayat = [];
while ($rr = $res_riwayat->fetch_assoc()) {
    $riwayat[] = [
        'nama'    => $rr['paket'],
        'nilai'   => $rr['nilai'],
        'benar'   => $rr['benar'],
        'total'   => $rr['total_soal'],
        'tanggal' => date('d M Y', strtotime($rr['dikerjakan'])),
        'grade'   => $rr['nilai'] >= 80 ? '🏆' : ($rr['nilai'] >= 60 ? '👍' : '📚'),
    ];
}

$stats = [
  ['num'=>6,            'label'=>'Materi Tersedia', 'icon'=>'📚', 'color'=>'#2563c7'],
  ['num'=>$total_tryout,'label'=>'Tryout Dikerjakan','icon'=>'📝','color'=>'#28a745'],
  ['num'=>$avg_nilai,   'label'=>'Rata-rata Nilai',  'icon'=>'🏅','color'=>'#f59e0b'],
  ['num'=>$total_forum, 'label'=>'Thread Forum',     'icon'=>'💬','color'=>'#7c3aed'],
];
?>

<main>
  <div class="page-header animate__animated animate__fadeIn">
    <h1>Dashboard</h1>
    <p>Selamat datang kembali, <strong><?= htmlspecialchars($nama_siswa) ?></strong>! 👋</p>
  </div>

  <div class="section">

    <!-- ── Statistik ── -->
    <div class="stats-grid">
      <?php foreach ($stats as $i => $s): ?>
      <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay:<?= $i*0.1 ?>s; border-top:3px solid <?= $s['color'] ?>">
        <div style="font-size:28px;margin-bottom:4px"><?= $s['icon'] ?></div>
        <div class="stat-num" style="color:<?= $s['color'] ?>"><?= $s['num'] ?></div>
        <div class="stat-label"><?= htmlspecialchars($s['label']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ── Progress belajar ── -->
    <h2 class="section-title">📊 Progress Belajar</h2>
    <?php if ($total_tryout === 0): ?>
      <div class="dashboard-card" style="text-align:center;padding:28px">
        <p style="color:#888;margin-bottom:12px">Belum ada data progress. Kerjakan tryout untuk melihat grafik progressmu!</p>
        <a href="tryout.php" class="btn-primary">Mulai Tryout →</a>
      </div>
    <?php else: ?>
      <?php foreach ($progress as $p): ?>
      <div class="progress-wrap animate__animated animate__fadeInLeft">
        <div class="progress-label">
          <span><?= htmlspecialchars($p['mata']) ?></span>
          <span style="color:<?= $p['color'] ?>;font-weight:700"><?= $p['persen'] ?>%</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" style="width:<?= $p['persen'] ?>%; background:<?= $p['color'] ?>; transition:width 1s ease"></div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- ── Riwayat tryout ── -->
    <h2 class="section-title" style="margin-top:36px">📋 Riwayat Tryout</h2>
    <?php if (empty($riwayat)): ?>
      <p style="color:#888;font-size:14px">Belum ada tryout. <a href="tryout.php" style="color:var(--biru)">Mulai tryout sekarang →</a></p>
    <?php else: ?>
    <div style="overflow-x:auto">
    <table class="tryout-table">
      <thead>
        <tr>
          <th></th>
          <th>Nama Tryout</th>
          <th>Nilai</th>
          <th>Benar</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($riwayat as $r): ?>
        <tr class="animate__animated animate__fadeIn">
          <td style="font-size:18px"><?= $r['grade'] ?></td>
          <td><?= htmlspecialchars($r['nama']) ?></td>
          <td class="nilai" style="color:<?= $r['nilai']>=80?'#28a745':($r['nilai']>=60?'#f59e0b':'#dc3545') ?>"><?= $r['nilai'] ?></td>
          <td style="font-size:13px;color:#888"><?= $r['benar'] ?>/<?= $r['total'] ?></td>
          <td><?= htmlspecialchars($r['tanggal']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>

    <!-- ── Quick Links ── -->
    <h2 class="section-title" style="margin-top:36px">⚡ Akses Cepat</h2>
    <div class="card-grid" style="grid-template-columns:repeat(3,1fr)">
      <a href="materi.php" class="dashboard-card" style="text-decoration:none;text-align:center;display:block">
        <div style="font-size:32px">📚</div>
        <p style="font-weight:600;margin-top:8px;color:var(--biru-tua)">Buka Materi</p>
      </a>
      <a href="tryout.php" class="dashboard-card" style="text-decoration:none;text-align:center;display:block">
        <div style="font-size:32px">📝</div>
        <p style="font-weight:600;margin-top:8px;color:var(--biru-tua)">Mulai Tryout</p>
      </a>
      <a href="forum.php" class="dashboard-card" style="text-decoration:none;text-align:center;display:block">
        <div style="font-size:32px">💬</div>
        <p style="font-weight:600;margin-top:8px;color:var(--biru-tua)">Forum Diskusi</p>
      </a>
    </div>

    <!-- ── Pengumuman ── -->
    <div class="dashboard-card animate__animated animate__fadeIn" style="border-left:4px solid var(--biru);margin-top:8px">
      <h2>📢 Pengumuman</h2>
      <p>Tryout interaktif kini tersedia — nilai otomatis setelah selesai! Forum diskusi juga sudah aktif. Yuk mulai belajar dan raih skor UTBK terbaikmu! 🚀</p>
    </div>

  </div>
</main>

<script>
// Animasi progress bar saat halaman load
window.addEventListener('load', function() {
  document.querySelectorAll('.progress-fill').forEach(function(el) {
    var w = el.style.width;
    el.style.width = '0%';
    setTimeout(function() { el.style.width = w; }, 300);
  });
});
</script>

<?php require_once '../includes/footer.php'; ?>
