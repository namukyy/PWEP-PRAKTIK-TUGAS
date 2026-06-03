<?php
session_start();
if (!isset($_SESSION['login'])) { header('Location: login.php'); exit; }

$page_title = 'Forum';
$base_path  = '../';
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? 0;

// ── CREATE: Buat thread baru ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action']??'') === 'buat_thread') {
    $judul    = trim($_POST['judul']    ?? '');
    $isi      = trim($_POST['isi']      ?? '');
    $kategori = trim($_POST['kategori'] ?? 'Umum');
    $allowed  = ['Matematika','Bahasa Indonesia','Bahasa Inggris','Penalaran Umum','Umum'];
    if (!in_array($kategori, $allowed)) $kategori = 'Umum';
    if (!empty($judul) && !empty($isi)) {
        $stmt = $conn->prepare("INSERT INTO forum_threads (user_id, judul, isi, kategori) VALUES (?,?,?,?)");
        $stmt->bind_param('isss', $user_id, $judul, $isi, $kategori);
        $stmt->execute();
        header('Location: forum.php?swal=created'); exit;
    }
}

// ── UPDATE: Edit thread ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action']??'') === 'edit_thread') {
    $tid   = (int)($_POST['thread_id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $isi   = trim($_POST['isi']   ?? '');
    if ($tid > 0 && !empty($judul) && !empty($isi)) {
        $stmt = $conn->prepare("UPDATE forum_threads SET judul=?, isi=? WHERE id=? AND user_id=?");
        $stmt->bind_param('ssii', $judul, $isi, $tid, $user_id);
        $stmt->execute();
        header("Location: forum.php?thread=$tid&swal=updated"); exit;
    }
}

// ── DELETE: Hapus thread ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action']??'') === 'hapus_thread') {
    $tid = (int)($_POST['thread_id'] ?? 0);
    $del = $conn->prepare("DELETE FROM forum_threads WHERE id=? AND user_id=?");
    $del->bind_param('ii', $tid, $user_id);
    $del->execute();
    header('Location: forum.php?swal=deleted'); exit;
}

// ── CREATE: Balas thread ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action']??'') === 'balas') {
    $tid = (int)($_POST['thread_id'] ?? 0);
    $isi = trim($_POST['isi'] ?? '');
    if ($tid > 0 && !empty($isi)) {
        $stmt = $conn->prepare("INSERT INTO forum_replies (thread_id, user_id, isi) VALUES (?,?,?)");
        $stmt->bind_param('iis', $tid, $user_id, $isi);
        $stmt->execute();
        header("Location: forum.php?thread=$tid&swal=replied"); exit;
    }
}

// ── UPDATE: Edit balasan ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action']??'') === 'edit_reply') {
    $rid = (int)($_POST['reply_id']  ?? 0);
    $tid = (int)($_POST['thread_id'] ?? 0);
    $isi = trim($_POST['isi'] ?? '');
    if ($rid > 0 && !empty($isi)) {
        $stmt = $conn->prepare("UPDATE forum_replies SET isi=? WHERE id=? AND user_id=?");
        $stmt->bind_param('sii', $isi, $rid, $user_id);
        $stmt->execute();
        header("Location: forum.php?thread=$tid&swal=reply_updated"); exit;
    }
}

// ── DELETE: Hapus balasan ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action']??'') === 'hapus_reply') {
    $rid = (int)($_POST['reply_id']  ?? 0);
    $tid = (int)($_POST['thread_id'] ?? 0);
    $del = $conn->prepare("DELETE FROM forum_replies WHERE id=? AND user_id=?");
    $del->bind_param('ii', $rid, $user_id);
    $del->execute();
    header("Location: forum.php?thread=$tid"); exit;
}

// ── READ: View single thread ─────────────────────────────
$view_thread = null; $replies = [];
if (isset($_GET['thread'])) {
    $tid  = (int)$_GET['thread'];
    $stmt = $conn->prepare("SELECT t.*, u.nama FROM forum_threads t JOIN users u ON t.user_id=u.id WHERE t.id=?");
    $stmt->bind_param('i', $tid);
    $stmt->execute();
    $view_thread = $stmt->get_result()->fetch_assoc();
    if ($view_thread) {
        $rs = $conn->prepare("SELECT r.*, u.nama FROM forum_replies r JOIN users u ON r.user_id=u.id WHERE r.thread_id=? ORDER BY r.created_at ASC");
        $rs->bind_param('i', $tid);
        $rs->execute();
        $replies = $rs->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// ── READ: Semua thread ───────────────────────────────────
$filter_kat  = $_GET['kat'] ?? '';
$allowed_kat = ['Matematika','Bahasa Indonesia','Bahasa Inggris','Penalaran Umum','Umum'];
if ($filter_kat && in_array($filter_kat, $allowed_kat)) {
    $stmt = $conn->prepare("SELECT t.*, u.nama, (SELECT COUNT(*) FROM forum_replies r WHERE r.thread_id=t.id) AS jml_balasan FROM forum_threads t JOIN users u ON t.user_id=u.id WHERE t.kategori=? ORDER BY t.created_at DESC");
    $stmt->bind_param('s', $filter_kat);
    $stmt->execute();
    $threads = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $threads = $conn->query("SELECT t.*, u.nama, (SELECT COUNT(*) FROM forum_replies r WHERE r.thread_id=t.id) AS jml_balasan FROM forum_threads t JOIN users u ON t.user_id=u.id ORDER BY t.created_at DESC")->fetch_all(MYSQLI_ASSOC);
}

$jml_thread  = $conn->query("SELECT COUNT(*) AS c FROM forum_threads")->fetch_assoc()['c'];
$jml_anggota = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$jml_balasan = $conn->query("SELECT COUNT(*) AS c FROM forum_replies")->fetch_assoc()['c'];

require_once '../includes/header.php';
?>

<main>
  <div class="page-header animate__animated animate__fadeIn">
    <h1>Forum Diskusi</h1>
    <p>Tanya jawab, berbagi tips, dan berdiskusi bersama siswa dan pengajar</p>
  </div>

  <!-- Statistik -->
  <div class="forum-stats">
    <div class="stat-box animate__animated animate__fadeInUp" style="animation-delay:.1s"><h3><?= $jml_thread ?></h3><p>Topik Diskusi</p></div>
    <div class="stat-box animate__animated animate__fadeInUp" style="animation-delay:.2s"><h3><?= $jml_anggota ?></h3><p>Anggota Aktif</p></div>
    <div class="stat-box animate__animated animate__fadeInUp" style="animation-delay:.3s"><h3><?= $jml_balasan ?></h3><p>Total Balasan</p></div>
  </div>

  <!-- Filter kategori -->
  <div class="forum-category">
    <a href="forum.php" class="forum-tag <?= !$filter_kat?'active':'' ?>">🗂 Semua</a>
    <?php foreach (['Matematika'=>'📘','Bahasa Indonesia'=>'📖','Bahasa Inggris'=>'🌍','Penalaran Umum'=>'🧠'] as $kat=>$ico): ?>
      <a href="forum.php?kat=<?= urlencode($kat) ?>" class="forum-tag <?= $filter_kat===$kat?'active':'' ?>"><?= $ico ?> <?= $kat ?></a>
    <?php endforeach; ?>
  </div>

  <div class="section">

  <?php if ($view_thread): ?>
    <!-- ══ DETAIL THREAD ══ -->
    <a href="forum.php" class="btn-back">← Kembali ke Forum</a>

    <div class="thread-detail animate__animated animate__fadeIn">
      <div class="thread-header">
        <span class="card-badge"><?= htmlspecialchars($view_thread['kategori']) ?></span>
        <h2 id="thread-judul-text"><?= htmlspecialchars($view_thread['judul']) ?></h2>
        <p class="thread-meta">Oleh <strong><?= htmlspecialchars($view_thread['nama']) ?></strong> · <?= date('d M Y H:i', strtotime($view_thread['created_at'])) ?></p>
      </div>
      <div class="thread-body">
        <p id="thread-isi-text"><?= nl2br(htmlspecialchars($view_thread['isi'])) ?></p>

        <?php if ($view_thread['user_id'] == $user_id): ?>
        <!-- Tombol Edit & Hapus Thread (hanya pemilik) -->
        <div style="display:flex;gap:8px;margin-top:14px">
          <button class="btn-edit-sm" onclick="toggleEditThread()">✏️ Edit</button>
          <form method="POST" style="display:inline" onsubmit="return confirmHapusThread(event)">
            <input type="hidden" name="action" value="hapus_thread" />
            <input type="hidden" name="thread_id" value="<?= $view_thread['id'] ?>" />
            <button type="submit" class="btn-danger-sm">🗑 Hapus</button>
          </form>
        </div>

        <!-- Form Edit Thread (tersembunyi) -->
        <div class="edit-form-wrap" id="edit-thread-form" style="display:none">
          <h4>✏️ Edit Thread</h4>
          <form method="POST" action="forum.php">
            <input type="hidden" name="action" value="edit_thread" />
            <input type="hidden" name="thread_id" value="<?= $view_thread['id'] ?>" />
            <div class="form-group">
              <label>Judul</label>
              <input type="text" name="judul" value="<?= htmlspecialchars($view_thread['judul']) ?>" required />
            </div>
            <div class="form-group">
              <label>Isi</label>
              <textarea name="isi" required><?= htmlspecialchars($view_thread['isi']) ?></textarea>
            </div>
            <div style="display:flex;gap:8px">
              <button type="submit" class="btn-primary">Simpan Perubahan</button>
              <button type="button" class="btn-outline-dark" onclick="toggleEditThread()">Batal</button>
            </div>
          </form>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Daftar balasan -->
    <h3 class="section-title" style="margin-top:30px"><?= count($replies) ?> Balasan</h3>

    <?php foreach ($replies as $rp): ?>
    <div class="reply-item animate__animated animate__fadeInUp" id="reply-<?= $rp['id'] ?>">
      <div class="reply-author">
        <span class="avatar-circle"><?= strtoupper(substr($rp['nama'],0,1)) ?></span>
        <div>
          <strong><?= htmlspecialchars($rp['nama']) ?></strong>
          <span class="reply-time"><?= date('d M Y H:i', strtotime($rp['created_at'])) ?></span>
        </div>
        <?php if ($rp['user_id'] == $user_id): ?>
        <!-- Edit & Hapus balasan -->
        <div style="margin-left:auto;display:flex;gap:6px">
          <button class="btn-edit-sm" onclick="toggleEditReply(<?= $rp['id'] ?>)">✏️</button>
          <form method="POST" style="display:inline" onsubmit="return confirmHapusReply(event)">
            <input type="hidden" name="action" value="hapus_reply" />
            <input type="hidden" name="reply_id" value="<?= $rp['id'] ?>" />
            <input type="hidden" name="thread_id" value="<?= $view_thread['id'] ?>" />
            <button type="submit" class="btn-danger-sm">🗑</button>
          </form>
        </div>
        <?php endif; ?>
      </div>

      <!-- Isi balasan -->
      <p id="reply-isi-<?= $rp['id'] ?>"><?= nl2br(htmlspecialchars($rp['isi'])) ?></p>

      <?php if ($rp['user_id'] == $user_id): ?>
      <!-- Form edit balasan (tersembunyi) -->
      <div class="edit-form-wrap" id="edit-reply-<?= $rp['id'] ?>" style="display:none">
        <h4>✏️ Edit Balasan</h4>
        <form method="POST" action="forum.php">
          <input type="hidden" name="action" value="edit_reply" />
          <input type="hidden" name="reply_id" value="<?= $rp['id'] ?>" />
          <input type="hidden" name="thread_id" value="<?= $view_thread['id'] ?>" />
          <div class="form-group">
            <textarea name="isi" required><?= htmlspecialchars($rp['isi']) ?></textarea>
          </div>
          <div style="display:flex;gap:8px">
            <button type="submit" class="btn-primary">Simpan</button>
            <button type="button" class="btn-outline-dark" onclick="toggleEditReply(<?= $rp['id'] ?>)">Batal</button>
          </div>
        </form>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <!-- Form balas -->
    <div class="reply-form-wrap">
      <h3>Tulis Balasan</h3>
      <form method="POST" action="forum.php">
        <input type="hidden" name="action" value="balas" />
        <input type="hidden" name="thread_id" value="<?= $view_thread['id'] ?>" />
        <textarea name="isi" placeholder="Tulis balasanmu di sini..." required></textarea>
        <button type="submit" class="btn-primary">Kirim Balasan</button>
      </form>
    </div>

  <?php else: ?>
    <!-- ══ DAFTAR THREAD ══ -->
    <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
      <h2 class="section-title" style="margin-bottom:0">Diskusi Terbaru</h2>
      <button class="btn-blue" onclick="toggleModal('modal-thread')">+ Buat Thread</button>
    </div>

    <div class="forum-list">
      <?php if (empty($threads)): ?>
        <p class="tw-text-gray tw-text-sm">Belum ada diskusi. Jadilah yang pertama bertanya!</p>
      <?php endif; ?>
      <?php foreach ($threads as $t): ?>
      <a href="forum.php?thread=<?= $t['id'] ?>" class="forum-item forum-item-link animate__animated animate__fadeInUp">
        <div>
          <span class="card-badge" style="margin-bottom:6px"><?= htmlspecialchars($t['kategori']) ?></span>
          <h3><?= htmlspecialchars($t['judul']) ?></h3>
          <p class="tw-text-sm tw-text-gray">Oleh <?= htmlspecialchars($t['nama']) ?></p>
        </div>
        <div class="forum-meta">
          <div><?= $t['jml_balasan'] ?> balasan</div>
          <div><?= date('d M Y', strtotime($t['created_at'])) ?></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  </div>
</main>

<!-- Modal Buat Thread -->
<div class="modal-overlay" id="modal-thread" style="display:none">
  <div class="modal-box animate__animated animate__zoomIn">
    <div class="modal-header">
      <h3>Buat Thread Baru</h3>
      <button class="modal-close" onclick="toggleModal('modal-thread')">✕</button>
    </div>
    <form method="POST" action="forum.php">
      <input type="hidden" name="action" value="buat_thread" />
      <div class="form-group">
        <label>Kategori</label>
        <select name="kategori" class="form-select">
          <option value="Matematika">📘 Matematika</option>
          <option value="Bahasa Indonesia">📖 Bahasa Indonesia</option>
          <option value="Bahasa Inggris">🌍 Bahasa Inggris</option>
          <option value="Penalaran Umum">🧠 Penalaran Umum</option>
          <option value="Umum">🗂 Umum</option>
        </select>
      </div>
      <div class="form-group">
        <label>Judul Pertanyaan</label>
        <input type="text" name="judul" placeholder="Tulis pertanyaanmu dengan jelas..." required />
      </div>
      <div class="form-group">
        <label>Detail Pertanyaan</label>
        <textarea name="isi" placeholder="Jelaskan lebih detail..." required></textarea>
      </div>
      <button type="submit" class="btn-primary tw-w-full">Posting Thread</button>
    </form>
  </div>
</div>

<script>
// ── SweetAlert notifications ─────────────────────────────
var swalParam = '<?= htmlspecialchars($_GET['swal'] ?? '') ?>';
var swalMap = {
  'created':      { icon:'success', title:'Thread Dibuat!',        text:'Pertanyaanmu berhasil diposting.' },
  'deleted':      { icon:'info',    title:'Thread Dihapus',        text:'' },
  'replied':      { icon:'success', title:'Balasan Terkirim!',     text:'' },
  'updated':      { icon:'success', title:'Thread Diperbarui!',    text:'Perubahan berhasil disimpan.' },
  'reply_updated':{ icon:'success', title:'Balasan Diperbarui!',   text:'' },
};
if (swalMap[swalParam]) {
  Swal.fire(Object.assign({ timer:2000, showConfirmButton:false }, swalMap[swalParam]));
}

function toggleModal(id) {
  var el = document.getElementById(id);
  el.style.display = el.style.display === 'none' ? 'flex' : 'none';
}

function toggleEditThread() {
  var f = document.getElementById('edit-thread-form');
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

function toggleEditReply(id) {
  var f = document.getElementById('edit-reply-' + id);
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

function confirmHapusThread(e) {
  e.preventDefault();
  var form = e.target;
  Swal.fire({
    title:'Hapus Thread?', text:'Thread dan semua balasannya akan dihapus permanen.',
    icon:'warning', showCancelButton:true,
    confirmButtonColor:'#d33', cancelButtonColor:'#aaa',
    confirmButtonText:'Ya, Hapus', cancelButtonText:'Batal'
  }).then(function(r){ if(r.isConfirmed) form.submit(); });
  return false;
}

function confirmHapusReply(e) {
  e.preventDefault();
  var form = e.target;
  Swal.fire({
    title:'Hapus Balasan?', icon:'warning', showCancelButton:true,
    confirmButtonColor:'#d33', cancelButtonColor:'#aaa',
    confirmButtonText:'Hapus', cancelButtonText:'Batal'
  }).then(function(r){ if(r.isConfirmed) form.submit(); });
  return false;
}

document.querySelectorAll('.modal-overlay').forEach(function(el){
  el.addEventListener('click', function(e){ if(e.target===el) el.style.display='none'; });
});
</script>

<?php require_once '../includes/footer.php'; ?>
