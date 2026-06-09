<?php
session_start();
if (!isset($_SESSION['login'])) { header('Location: login.php'); exit; }

$page_title = 'Tryout';
$base_path  = '../';
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? 0;

// ── AJAX: Simpan hasil tryout ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'simpan_hasil') {
    $paket       = trim($_POST['paket']       ?? '');
    $nilai       = (int)($_POST['nilai']       ?? 0);
    $total_soal  = (int)($_POST['total_soal']  ?? 0);
    $benar       = (int)($_POST['benar']       ?? 0);
    $waktu_menit = (int)($_POST['waktu_menit'] ?? 0);
    if (!empty($paket) && $total_soal > 0) {
        $stmt = $conn->prepare("INSERT INTO tryout_results (user_id, paket, nilai, total_soal, benar, waktu_menit) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('isiiii', $user_id, $paket, $nilai, $total_soal, $benar, $waktu_menit);
        $stmt->execute();
    }
    echo json_encode(['ok'=>true]);
    exit;
}

// ── Data soal per paket ──────────────────────────────────
$soal_bank = [
  'Tryout UTBK #1 – Penalaran Umum' => [
    ['q'=>'Semua mahasiswa rajin belajar. Budi adalah mahasiswa. Kesimpulan yang tepat adalah...','a'=>['Budi mungkin rajin belajar','Budi pasti rajin belajar','Budi tidak rajin belajar','Budi kadang rajin belajar'],'j'=>1],
    ['q'=>'Jika hari ini hujan, maka jalanan basah. Jalanan tidak basah. Maka...','a'=>['Hari ini hujan','Hari ini tidak hujan','Jalanan kering','Hujan deras'],'j'=>1],
    ['q'=>'Urutan: 2, 4, 8, 16, ... angka berikutnya adalah...','a'=>['24','28','32','36'],'j'=>2],
    ['q'=>'Analogi: Dokter : Rumah Sakit = Guru : ...','a'=>['Buku','Sekolah','Murid','Kantor'],'j'=>1],
    ['q'=>'Jika A lebih tinggi dari B, dan C lebih pendek dari B, maka...','a'=>['A lebih pendek dari C','C lebih tinggi dari A','A lebih tinggi dari C','Tidak dapat ditentukan'],'j'=>2],
  ],
  'Tryout UTBK #2 – Pengetahuan Umum' => [
    ['q'=>'Pancasila sebagai dasar negara Indonesia disahkan pada tanggal...','a'=>['17 Agustus 1945','18 Agustus 1945','1 Juni 1945','22 Juni 1945'],'j'=>1],
    ['q'=>'Presiden pertama Republik Indonesia adalah...','a'=>['Mohammad Hatta','Soekarno','Soeharto','Habibie'],'j'=>1],
    ['q'=>'Semboyan negara Indonesia adalah...','a'=>['Bhinneka Tunggal Ika','Tut Wuri Handayani','Garuda Pancasila','NKRI'],'j'=>0],
    ['q'=>'Ibu kota Indonesia yang baru adalah...','a'=>['Jakarta','Surabaya','Nusantara','Bandung'],'j'=>2],
    ['q'=>'Laut terluas di dunia adalah...','a'=>['Atlantik','Hindia','Arktik','Pasifik'],'j'=>3],
  ],
  'Tryout UTBK #3 – Matematika' => [
    ['q'=>'Akar-akar persamaan x² - 5x + 6 = 0 adalah...','a'=>['x=1 dan x=6','x=2 dan x=3','x=-2 dan x=-3','x=3 dan x=4'],'j'=>1],
    ['q'=>'Nilai diskriminan dari 2x² + 3x - 2 = 0 adalah...','a'=>['25','9','7','1'],'j'=>0],
    ['q'=>'Rata-rata dari data 4, 6, 8, 10, 12 adalah...','a'=>['7','8','9','10'],'j'=>1],
    ['q'=>'Modus dari data 3, 3, 4, 5, 5, 5, 6, 7 adalah...','a'=>['3','4','5','6'],'j'=>2],
    ['q'=>'Hasil dari 3² + 4² = ...','a'=>['25','20','49','14'],'j'=>0],
  ],
  'Tryout UTBK #4 – Bahasa Indonesia' => [
    ['q'=>'Kata "efektif" bersinonim dengan...','a'=>['efisien','bermanfaat','tepat guna','produktif'],'j'=>2],
    ['q'=>'Kalimat yang menggunakan ejaan benar adalah...','a'=>['Saya pergi ke-sekolah','Saya pergi kesekolah','Saya pergi ke sekolah','Saya pergi Ke Sekolah'],'j'=>2],
    ['q'=>'Antonim dari kata "eksplisit" adalah...','a'=>['jelas','nyata','implisit','tepat'],'j'=>2],
    ['q'=>'Imbuhan "me-" pada kata "menulis" berfungsi sebagai...','a'=>['awalan membentuk kata benda','awalan membentuk kata kerja aktif','awalan membentuk kata sifat','akhiran'],'j'=>1],
    ['q'=>'Paragraf yang kalimat utamanya berada di akhir disebut paragraf...','a'=>['deduktif','induktif','campuran','naratif'],'j'=>1],
  ],
];

$tryout_list = [
  ['judul'=>'Tryout UTBK #1 – Penalaran Umum',   'soal'=>5,'durasi'=>'60 menit','peserta'=>1240],
  ['judul'=>'Tryout UTBK #2 – Pengetahuan Umum', 'soal'=>5,'durasi'=>'75 menit','peserta'=>987],
  ['judul'=>'Tryout UTBK #3 – Matematika',        'soal'=>5,'durasi'=>'45 menit','peserta'=>1102],
  ['judul'=>'Tryout UTBK #4 – Bahasa Indonesia',  'soal'=>5,'durasi'=>'60 menit','peserta'=>854],
];

require_once '../includes/header.php';
?>

<main>
  <div class="page-header animate__animated animate__fadeIn">
    <h1>Tryout Online</h1>
    <p>Latih kemampuanmu dengan soal pilihan ganda dan dapatkan nilai secara otomatis</p>
  </div>

  <div class="section">
    <h2 class="section-title">Pilih Paket Tryout</h2>
    <div class="tryout-grid">
      <?php foreach ($tryout_list as $idx => $t): ?>
      <div class="tryout-card animate__animated animate__fadeInUp" style="animation-delay:<?= $idx*0.1 ?>s">
        <h3><?= htmlspecialchars($t['judul']) ?></h3>
        <div class="tryout-meta">
          <span>📝 <?= $t['soal'] ?> soal</span>
          <span>⏱ <?= htmlspecialchars($t['durasi']) ?></span>
          <span>👥 <?= number_format($t['peserta']) ?> peserta</span>
        </div>
        <button class="btn-primary" onclick="konfirmasiMulai(<?= $idx ?>)">Mulai Tryout</button>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<!-- Modal Tryout -->
<div class="modal-overlay" id="modal-tryout" style="display:none">
  <div class="modal-box modal-tryout-box">
    <div class="tryout-quiz-header">
      <div>
        <h3 id="qt-judul"></h3>
        <p id="qt-progress" style="font-size:13px;color:#888;margin-top:4px"></p>
      </div>
      <div class="tryout-timer" id="qt-timer">60:00</div>
    </div>
    <div id="qt-soal-wrap"></div>
    <div class="tryout-nav">
      <button class="btn-outline-dark" id="btn-prev" onclick="navSoal(-1)" style="display:none">← Sebelumnya</button>
      <button class="btn-blue" id="btn-next" onclick="navSoal(1)">Selanjutnya →</button>
      <button class="btn-primary" id="btn-selesai" onclick="konfirmasiSelesai()" style="display:none">✔ Selesai</button>
    </div>
  </div>
</div>

<!-- Modal Review -->
<div class="modal-overlay" id="modal-review" style="display:none">
  <div class="modal-box" style="max-width: 640px; padding: 24px;">
    <div class="modal-header">
      <h3>🔍 Pembahasan Tryout</h3>
      <button class="modal-close" onclick="tutupReview()">✕</button>
    </div>
    <div id="review-info" style="margin-bottom: 16px;">
      <h4 id="review-judul-paket" style="font-size: 16px; font-weight: 700; color: var(--biru-tua);"></h4>
    </div>
    <div id="review-soal-list" style="display: flex; flex-direction: column; gap: 20px; max-height: 55vh; overflow-y: auto; padding-right: 8px;">
      <!-- list of review questions will be injected here -->
    </div>
    <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px;">
      <button class="btn-outline-dark" onclick="tutupReview()">Tutup</button>
      <button class="btn-primary" onclick="location.href='dashboard.php'">Ke Dashboard</button>
    </div>
  </div>
</div>

<script>
var soalBank    = <?= json_encode($soal_bank) ?>;
var tryoutList  = <?= json_encode(array_column($tryout_list,'judul')) ?>;
var currentPaket= null, currentSoal=0, jawaban={}, timerInterval=null, sisaDetik=0;

function konfirmasiMulai(idx) {
  Swal.fire({
    title: tryoutList[idx],
    html: '<p style="color:#555">5 soal · Waktu 60 menit</p><p style="margin-top:8px;font-size:13px;color:#888">Pastikan kamu siap sebelum memulai!</p>',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '🚀 Mulai Sekarang',
    cancelButtonText: 'Nanti Dulu',
    confirmButtonColor: '#2563c7'
  }).then(function(r){ if(r.isConfirmed) mulaiTryout(idx); });
}

function mulaiTryout(idx) {
  currentPaket = tryoutList[idx];
  currentSoal  = 0;
  jawaban      = {};
  sisaDetik    = 3600;
  updateTimer();
  clearInterval(timerInterval);
  timerInterval = setInterval(function(){
    sisaDetik--;
    updateTimer();
    if(sisaDetik<=0){ clearInterval(timerInterval); selesaiTryout(); }
  }, 1000);
  renderSoal();
  document.getElementById('modal-tryout').style.display = 'flex';
}

function updateTimer() {
  var m=Math.floor(sisaDetik/60), s=sisaDetik%60;
  var txt=(m<10?'0':'')+m+':'+(s<10?'0':'')+s;
  var el=document.getElementById('qt-timer');
  el.textContent=txt;
  el.style.background= sisaDetik<300 ? '#dc3545' : '';
}

function renderSoal() {
  var soal=soalBank[currentPaket], total=soal.length, s=soal[currentSoal];
  document.getElementById('qt-progress').textContent='Soal '+(currentSoal+1)+' dari '+total;
  var huruf=['A','B','C','D'];
  var html='<div class="soal-box"><p class="soal-nomor">Soal '+(currentSoal+1)+'</p>';
  html+='<p class="soal-text">'+escHtml(s.q)+'</p><div class="pilihan-grid">';
  s.a.forEach(function(opt,i){
    var sel=jawaban[currentSoal]===i?'pilihan-selected':'';
    html+='<button class="pilihan-btn '+sel+'" onclick="pilihJawaban('+i+')">'
         +'<span class="pilihan-huruf">'+huruf[i]+'</span><span>'+escHtml(opt)+'</span></button>';
  });
  html+='</div></div>';
  document.getElementById('qt-soal-wrap').innerHTML=html;
  document.getElementById('btn-prev').style.display   = currentSoal>0?'inline-block':'none';
  document.getElementById('btn-next').style.display   = currentSoal<total-1?'inline-block':'none';
  document.getElementById('btn-selesai').style.display= currentSoal===total-1?'inline-block':'none';
}

function pilihJawaban(i){ jawaban[currentSoal]=i; renderSoal(); }
function navSoal(d){ var t=soalBank[currentPaket].length; currentSoal=Math.max(0,Math.min(t-1,currentSoal+d)); renderSoal(); }

function konfirmasiSelesai() {
  var total=soalBank[currentPaket].length;
  var dijawab=Object.keys(jawaban).length;
  if(dijawab<total){
    Swal.fire({
      title:'Masih ada soal belum dijawab',
      html:'<p>'+(total-dijawab)+' soal belum dijawab. Tetap selesai?</p>',
      icon:'warning',
      showCancelButton:true,
      confirmButtonText:'Selesai',
      cancelButtonText:'Kembali',
      confirmButtonColor:'#2563c7'
    }).then(function(r){ if(r.isConfirmed) selesaiTryout(); });
  } else { selesaiTryout(); }
}

function selesaiTryout() {
  clearInterval(timerInterval);
  document.getElementById('modal-tryout').style.display='none';
  var soal=soalBank[currentPaket], benar=0;
  soal.forEach(function(s,i){ if(jawaban[i]===s.j) benar++; });
  var total=soal.length, nilai=Math.round((benar/total)*100), waktu=Math.max(1,Math.round((3600-sisaDetik)/60));

  var form=new FormData();
  form.append('action','simpan_hasil');
  form.append('paket',currentPaket);
  form.append('nilai',nilai);
  form.append('total_soal',total);
  form.append('benar',benar);
  form.append('waktu_menit',waktu);
  fetch('tryout.php',{method:'POST',body:form});

  var ikon=nilai>=80?'🏆':nilai>=60?'👍':'📚';
  var ket=nilai>=80?'Luar Biasa!':nilai>=60?'Bagus!':'Terus Berlatih!';
  Swal.fire({
    title: ikon+' '+ket,
    html: '<div style="display:flex;gap:24px;justify-content:center;margin:16px 0">'
        + '<div><div style="font-size:36px;font-weight:700;color:#2563c7">'+nilai+'</div><small>Nilai</small></div>'
        + '<div><div style="font-size:36px;font-weight:700;color:#28a745">'+benar+'/'+total+'</div><small>Benar</small></div>'
        + '<div><div style="font-size:36px;font-weight:700;color:#f59e0b">'+waktu+'</div><small>Menit</small></div>'
        + '</div>'
        + '<p style="color:#888;font-size:13px">Hasil disimpan ke riwayatmu</p>'
        + '<button class="btn-blue" onclick="bukaReview()" style="margin-top:12px; width:100%; display:block; padding:10px 14px; border-radius:6px; font-weight:600; cursor:pointer; text-align:center">🔍 Lihat Pembahasan Soal</button>',
    icon: nilai>=60 ? 'success' : 'info',
    confirmButtonText: 'Lihat Dashboard',
    showCancelButton: true,
    cancelButtonText: 'Tutup',
    confirmButtonColor: '#2563c7'
  }).then(function(r){ if(r.isConfirmed) location.href='dashboard.php'; });
}

function bukaReview() {
  Swal.close();
  var soal = soalBank[currentPaket];
  var html = '';
  var huruf = ['A', 'B', 'C', 'D'];
  
  document.getElementById('review-judul-paket').textContent = currentPaket;
  
  soal.forEach(function(s, i) {
    var userJawab = jawaban[i];
    var benarJawab = s.j;
    
    html += '<div style="border-bottom: 1.5px solid #eee; padding-bottom: 16px; margin-bottom: 12px;">';
    html += '  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px;">';
    html += '    <span style="font-size: 12px; font-weight: 700; color: var(--biru); text-transform: uppercase;">Soal ' + (i + 1) + '</span>';
    
    if (userJawab === undefined) {
      html += '    <span style="background: #fef3c7; color: #d97706; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 12px;">TIDAK DIJAWAB</span>';
    } else if (userJawab === benarJawab) {
      html += '    <span style="background: #d1e7dd; color: #0f5132; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 12px;">BENAR</span>';
    } else {
      html += '    <span style="background: #f8d7da; color: #842029; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 12px;">SALAH</span>';
    }
    
    html += '  </div>';
    html += '  <p style="font-size: 14px; font-weight: 600; color: #222; line-height: 1.5; margin-bottom: 12px;">' + escHtml(s.q) + '</p>';
    html += '  <div style="display: flex; flex-direction: column; gap: 8px;">';
    
    s.a.forEach(function(opt, oIdx) {
      var extraStyle = '';
      var letterBg = '';
      var icon = '';
      
      if (oIdx === benarJawab) {
        extraStyle = 'border-color: #28a745; background: #f0fdf4; color: #166534; font-weight: 600;';
        letterBg = 'background: #28a745; color: #fff;';
        icon = ' <span style="color: #28a745; margin-left: auto; font-weight: bold;">✔ Kunci Jawaban</span>';
      } else if (oIdx === userJawab) {
        extraStyle = 'border-color: #dc3545; background: #fef2f2; color: #991b1b;';
        letterBg = 'background: #dc3545; color: #fff;';
        icon = ' <span style="color: #dc3545; margin-left: auto; font-weight: bold;">❌ Pilihanmu</span>';
      }
      
      html += '    <div class="pilihan-btn" style="cursor: default; pointer-events: none; display: flex; align-items: center; width: 100%; ' + extraStyle + '">';
      html += '      <span class="pilihan-huruf" style="margin-right: 12px; ' + letterBg + '">' + huruf[oIdx] + '</span>';
      html += '      <span>' + escHtml(opt) + '</span>';
      html += icon;
      html += '    </div>';
    });
    
    html += '  </div>';
    html += '</div>';
  });
  
  document.getElementById('review-soal-list').innerHTML = html;
  document.getElementById('modal-review').style.display = 'flex';
}

function tutupReview() {
  document.getElementById('modal-review').style.display = 'none';
}

function escHtml(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

<?php require_once '../includes/footer.php'; ?>
