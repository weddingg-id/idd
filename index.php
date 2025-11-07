<?php
$data_file = "data.txt";
if (!file_exists($data_file)) {
    die("Data belum ada. Silakan isi dari admin.php dulu.");
}

$data = json_decode(file_get_contents($data_file), true);
if (!$data) die("Format data.txt tidak valid JSON.");
$tamu = isset($_GET['tamu']) ? htmlspecialchars($_GET['tamu']) : 'TamuUndangan';

$tanggal = $data['countdown']['tanggal'] ?? '';
$waktu = $data['countdown']['waktu'] ?? '';

$event_date = ($tanggal && $waktu) ? "$tanggal $waktu" : null;


$file = '0x1.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = htmlspecialchars($_POST['name']);
  $msg = htmlspecialchars($_POST['msg']);
  $confirm = htmlspecialchars($_POST['confirm']);
  $initial = strtoupper(substr($name, 0, 1));

  $icon = ($confirm === 'Tidak Hadir') ? '❌' : (($confirm === 'Akan Hadir') ? '⏳' : '✔️');

  $data = [
    'initial' => $initial,
    'name' => $name,
    'msg' => $msg,
    'confirm' => $confirm,
    'icon' => $icon
  ];

  // Simpan ke file JSON
  $all = [];
  if (file_exists($file)) {
    $all = json_decode(file_get_contents($file), true);
  }
  array_unshift($all, $data);
  file_put_contents($file, json_encode($all));

  // Redirect agar tidak dobel
 session_start();
$_SESSION['scrolled'] = true;
header("Location: " . $_SERVER['PHP_SELF']);
exit;
}

// Ambil semua data untuk ditampilkan
$list = [];
if (file_exists($file)) {
  $list = json_decode(file_get_contents($file), true);
}

?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

<title><?= htmlspecialchars($data['judul']) ?></title>
<meta property="og:title" content="Undangan Pernikahan <?= htmlspecialchars($data['pria']['nama']) ?> &amp; <?= htmlspecialchars($data['wanita']['nama']) ?> 💍" />
<meta property="og:description" content="Kami mengundang <?= $tamu ?> untuk hadir di hari bahagia kami." />
<meta property="og:image" content="'<?= $data['sampul'] ?>" />
    <!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Rubik:wght@300;400;600&display=swap" rel="stylesheet">
<!-- Animate / AOS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

<style>
:root{
  --accent:#b1744a;
  --bg:#fffaf6;
  --muted:#6b6b6b;
  --card:#ffffff;
  --gold1:#f9e7b6;
  --gold2:#d4af37;
  --ivory:#fdfbf8;
  --shadow:rgba(0,0,0,.15);
}
*{box-sizing:border-box}
body{
    padding-bottom: env(safe-area-inset-bottom);
  margin:0;
  font-family:Rubik,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;
  background:var(--bg);
  color:#222;
  overflow-x:hidden;
  scroll-behavior:smooth;
}

/* Modal pembuka */
.modalx{
  position:fixed; inset:0; display:flex; align-items:center; justify-content:center;
  background-size:cover; background-position:center; z-index:9999;
}
.overlayy{position:absolute; inset:0; background:rgba(0,0,0,0.52);}
.content-modalx{
  position:relative; z-index:2; width:92%; max-width:620px;
  padding:28px; border-radius:12px; text-align:center; color:#fff;
  backdrop-filter:blur(6px);
  background:rgba(255,255,255,0.05);
  box-shadow:0 20px 60px rgba(0,0,0,0.45);
}
.content-modalx h1{font-family:'Playfair Display',serif;margin:0;font-size:34px;}
.content-modalx .mempelai{font-size:20px;margin-top:8px;font-weight:500;}
.content-modalx .tgl{margin-top:6px;color:#f0e8df;font-size:15px;}
.wdp-button-wrapper{margin-top:18px;}
.wdp-button-wrapper button{
  padding:12px 22px;border-radius:10px;border:0;background:var(--accent);color:#fff;
  font-weight:700;cursor:pointer;font-size:15px;
  box-shadow:0 10px 28px rgba(177,116,74,0.22);
  transition:transform .25s ease;
}
.wdp-button-wrapper button:hover{transform:scale(1.06);}
.wdp-keterangan{margin-top:12px;font-size:13px;color:#f7efe8;opacity:0.92;}

/* ===== HERO SECTION ===== */
header.hero{
  min-height: 100dvh;
  position: relative;
  width: 100%;
  height: auto;
  background: url('<?= $data['sampul'] ?>') no-repeat center center/cover;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  color: #fff;
  overflow: hidden;
}
header.hero::before {
  content: "";
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.45);
  z-index: 1;
}
header.hero .hero-text {
  position: relative;
  z-index: 2;
  padding: 20px;
}
header.hero .hero-text h2 {
  font-family: 'Playfair Display', serif;
  font-size: 25px;
    color: white;
  margin: 0;
}
header.hero .hero-text h3 {
    font-family: 'Playfair Display', serif;
    font-size: 25px;
    margin: 0;
    }
header.hero .hero-text p {
  font-size: 16px;
  margin-top: 8px;
  color: #f0e8df;
}


 /* Pastikan kontainer utama countdown di tengah */
.cont {
  margin: 20px auto;              /* ini kunci biar di tengah horizontal */
  background: #fff5e9;
  border-radius: 20px;
  box-shadow: 0 4px 10px var(--shadow);
  padding: 20px 20px;
  text-align: center;
  width: 100%;
  max-width: 420px;
  display: flex;                  /* tambahkan flex */
  flex-direction: column;
  align-items: center;            /* tengah horizontal */
  justify-content: center;        /* tengah vertikal (jika tinggi lebih besar) */
}

/* Countdown box biar tetap rapi di tengah */
.countdown {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin: 0 auto;
  text-align: center;
    }
    
.count-box {
  background: var(--box-bg);

  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);

  transition: transform 0.2s ease;
    
  border-radius: 15px;
  padding: 18px 22px; /* lebih tinggi dan sedikit lebar */
  min-width: 70px;    /* lebarkan tiap kotak */
  height: 90px;       /* buat tinggi seragam */
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.count-box:hover {
  transform: translateY(-3px);
}

.count-value {
  font-size: 30px;
  font-weight: 700;
  color: var(--accent);
  margin-bottom: 4px;
}

.count-label {
  font-size: 13px;
  color: var(--text-sub);
}

/* Responsif */
@media (max-width: 480px) {
  .container { padding: 24px 18px; }
  .countdown { gap: 6px; }
  .count-value { font-size: 24px; }
  .count-label { font-size: 12px; }
}
    
    
/* ==== GAYA PASANGAN (ARCH STYLE) ==== */
.pair {
  background-color: #e4b999;
  text-align: center;
  padding: 60px 15px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 40px;
}
.card-arch {
  background-color: #e4b999;
  color: #fff;
  max-width: 360px;
  font-family: 'Playfair Display', serif;
  flex: 1 1 300px;
}
.photo-wrap {
  width: 100%;
  overflow: hidden;
  border-top-left-radius: 200px;
  border-top-right-radius: 200px;
  height: 520px;
  position: relative;
  box-shadow: 0 8px 18px rgba(0,0,0,0.25);
}
.photo-wrap img {
  width: 100%;
  height: auto;
  object-fit: cover;
  border-top-left-radius: 200px;
  border-top-right-radius: 200px;
  display: block;
  transition: transform 0.8s ease;
}
.photo-wrap:hover img {
  transform: scale(1.05);
}
.card-arch h3 {
  font-size: 23px;
  margin: 20px 0 10px;
  color: #fff;
  font-style: italic;
}
.card-arch p {
  color: #fff;
  font-size: 16px;
  line-height: 1.5;
  font-family: 'Rubik', sans-serif;
}

/* ==== EVENTS & MAP ==== */
.events {
  background: #fae4d3;
  text-align: center;
  padding: 70px 15px;
  font-family: 'Playfair Display', serif;
}
.events h2 {
  font-size: 32px;
  margin: 0;
  color: #3e2a1d;
}
.events .ornament {
  width: 100px;
  margin: 0 auto 16px;
  filter: brightness(0) saturate(100%) invert(21%) sepia(11%) saturate(704%) hue-rotate(357deg) brightness(90%) contrast(91%);
}
.events .columns {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 40px;
  margin-top: 40px;
}
.events .column {
  flex: 1 1 260px;
  background: rgba(255,255,255,0.5);
  border-radius: 16px;
  padding: 20px;
}
.events .column h3 {
  color: #b1744a;
  font-size: 28px;
  margin: 0 0 10px;
  font-style: italic;
}
.events .column p {
  margin: 5px 0;
  font-family: 'Rubik', sans-serif;
  color: #3b2b22;
  line-height: 1.6;
}
.events .address {
  font-family: 'Rubik', sans-serif;
  margin-top: 20px;
  font-size: 16px;
  color: #000;
}
.map {
  margin-top: 30px;
  border-radius: 12px;
  overflow: hidden;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

/* ==== Gallery & Footer ==== */
.gallery{
  max-width:980px;margin:20px auto;padding:0 12px 40px;
  display:flex;gap:16px;justify-content:center;flex-wrap:wrap;
}
.gallery img{
  width:320px;height:220px;object-fit:cover;border-radius:12px;
  box-shadow:0 10px 26px rgba(0,0,0,0.06);
  transition:transform 0.4s ease, box-shadow 0.4s ease;
}
.gallery img:hover{
  transform:scale(1.04);
  box-shadow:0 10px 26px rgba(0,0,0,0.2);
}
.section{max-width:980px;margin:16px auto;padding:0 18px 24px;color:var(--muted);line-height:1.6;}
footer{padding:28px 12px;text-align:center;color:var(--muted);}
@media(max-width:768px){
  .photo-wrap{height:450px;}
}
/* Section Turut Mengundang */
.keluarga-section {
    padding-top: -20px;
    text-align: center;
    padding: 60px 20px;
}
.keluarga-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 35px;
    font-weight: 600;
    color: #8a572d;
    margin-bottom: 6px;
}

.keluarga-section .subtitle {
    font-style: italic;
    font-size: 15px;
    color: #6b6b6b;
    margin-bottom: 40px;
}

.keluarga-title {
    font-family: 'Playfair Display', serif;
    font-size: 17px;
    color: #8a572d;
    font-weight: 600;
    margin-bottom: 0px;
}

.keluarga-divider {
    width: 70px;
    height: 1px;
    background: #d4b26f;
    border-radius: 2px;
    margin: 10px auto 10px;
}

.keluarga-names {
    display: block;
    justify-content: center;
    gap: 26px;
    flex-wrap: wrap;
}

.keluarga-names span {
    display: block;
    margin-top: 5px;
    font-size: 16px;
    font-family: 'Rubik', sans-serif;
    color: #555;
    padding: 3px 6px;
}
.keluarga-block {
    margin-top: 40px;
    }

/* Mobile */
@media (max-width: 560px) {
    .keluarga-section h2 {
        font-size: 25px;
    }
    .keluarga-section .subtitle {
        font-size: 13px;
    }
    .keluarga-names span {
        font-size: 14px;
    }
}

.judul-berbahagia {
  font-family: 'Great Vibes', cursive;
  font-size: 28px;
  color: #3e2a1d; /* bisa diganti ke #7a5c3f untuk lebih coklat lembut */
  text-align: center;
  margin: 25px 0;
  letter-spacing: 0.5px;
}

.judul-gallery {
  font-family: 'Great Vibes', cursive;
  font-size: 32px;
  text-align: center;
  color: #3e2a1d; /* bisa ubah ke #7a5c3f untuk warna coklat lembut */
  margin: 40px 0 20px;
  letter-spacing: 0.5px;
}
    .contan{
      max-width:780px;
      margin:0 auto;
      padding:30px 20px 0px;
    }
    header{
      display:flex;
      flex-direction:column;
      align-items:center;
      text-align:center;
      margin-bottom:30px;
    }
    .dec{
      display:flex;align-items:center;gap:18px;margin-bottom:10px;
    }
    .dec .line{
      height:4px;width:80px;
      background:linear-gradient(90deg,rgba(0,0,0,0.05),transparent);
      border-radius:2px;
    }
    .heart{
      width:46px;height:46px;
      border-radius:50%;
      background:rgba(0,0,0,0.04);
      display:flex;align-items:center;justify-content:center;
      font-size:22px;color:#e0a884;
    }
    h1{
      font-family:Montserrat,serif;
      font-weight:600;
      letter-spacing:1px;
      font-size:20px;
      color:#2b2b2b;
    }
    form{
      background:#fff;
      border-radius:16px;
      box-shadow:var(--shadow);
      padding:26px;
      position:relative;
      z-index:5;
    }
    label{
      display:block;
      margin:14px 0 6px;
      font-size:14px;
      color:#333;
      font-weight:600;
    }
    input[type=text],textarea,.selectbox{
      width:100%;
      padding:12px 14px;
      border-radius:10px;
      border:1px solid rgba(0,0,0,0.08);
      background:#fff;
      box-shadow:inset 0 1px 3px rgba(0,0,0,0.05);
      font-size:15px;
      transition:border .2s ease;
    }
    input:focus,textarea:focus,.selectbox:focus{
      outline:none;border:1px solid var(--accent);
    }
    textarea{min-height:110px;resize:vertical;}
    .btn{
      width:100%;margin-top:22px;padding:14px 22px;
      border:none;border-radius:10px;
      background:var(--accent);color:#fff;
      font-weight:600;cursor:pointer;
      box-shadow:0 5px 10px rgba(246,179,25,0.3);
      transition:transform .25s ease,box-shadow .25s ease;
    }
    .btn:hover{transform:translateY(-2px);box-shadow:0 7px 16px rgba(246,179,25,0.4);}

    /* Divider SVG */
.messages {
  max-height: 350px; /* atur tinggi area scroll */
  overflow-y: auto;
  margin-top: 20px;
  padding-right: 10px;
  scroll-behavior: smooth;
}

/* optional: biar scrollbar-nya kelihatan halus */
.messages::-webkit-scrollbar {
  width: 2px;
}
.messages::-webkit-scrollbar-thumb {
  background-color: #d4af37; /* warna emas lembut */
  border-radius: 4px;
}
.messages::-webkit-scrollbar-track {
  background: transparent;
}

 .msg {
     align-items: flex-start; /* biar avatar tetap di atas, gak ketarik tengah */
  background: #fff;
  margin: 10px 0;
  padding: 12px;
  border-radius: 12px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.06);
  display: flex;
  align-items: flex-start;
  gap: 10px;
}
.avatar {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ececec;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
  color: #666;
  margin-top: 2px; /* opsional, biar sejajar rapi */
    }

.msg b {
  font-size: 13px;   /* nama lebih kecil */
  font-weight: 600;
  color: #222;
}

.badge {
  background: #d4a017;
  color: #fff;
  border-radius: 5px;
  padding: 1px 6px;   /* kecilkan padding */
  font-size: 10.5px;  /* badge kecil banget */
  display: inline-flex;
  align-items: center;
  gap: 3px;
  margin-left: 4px;
}

.msg div:last-child {
  font-size: 13px;   /* pesan ucapan */
  line-height: 1.4;
  color: #444;
  margin-top: 4px;
}

    /* Modal */
    .modal-backdrop{
      position:fixed;inset:0;
      background:rgba(0,0,0,0.55);
      display:none;
      align-items:center;justify-content:center;
      z-index:40;
    }
    .modal{
      width:90%;max-width:400px;
      background:#fff;border-radius:12px;
      box-shadow:0 8px 24px rgba(0,0,0,0.25);
      overflow:hidden;
      animation:scaleIn .3s ease;
    }
    .modal .opt{
      padding:18px 20px;
      border-bottom:1px solid rgba(0,0,0,0.05);
      display:flex;align-items:center;justify-content:space-between;
      cursor:pointer;
    }
    .modal .opt:hover{background:#f6f6f6;}
    .modal .opt:last-child{border-bottom:none;}
    .radio{
      width:20px;height:20px;
      border-radius:50%;
      border:2px solid rgba(0,0,0,0.25);
      display:inline-block;
    }
    .radio.selected{
      border-color:var(--accent);
      box-shadow:0 0 0 4px rgba(246,179,25,0.25);
    }

    @keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
    @keyframes scaleIn{from{transform:scale(0.9);opacity:0;}to{transform:scale(1);opacity:1);}}
  #modalx {
  transition: opacity: 7s ease;
}
#modalx.fade-out {
  opacity: 0;
  pointer-events: none;
    }
    

   #cashless {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  text-align: center;
  padding: 60px 20px;
    }
    
 /* CARD */
.card{
  background:rgba(255,255,255,.95);
  border-radius:28px;
  box-shadow:0 10px 40px var(--shadow);
  padding:55px 40px 65px;
  max-width:380px;
  text-align:center;
  position:relative;
  overflow:hidden;
}

/* SVG ORNAMENT */
.card::before,
.card::after{
  content:'';
  position:absolute;
  left:50%;
  transform:translateX(-50%);
  width:90%;
  height:70px;
  background-repeat:no-repeat;
  background-position:center;
  background-size:contain;
  opacity:.85;
}
.card::before{
  top:-20px;
  background-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 70"><defs><linearGradient id="gold" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="%23f8e7a1"/><stop offset="50%" stop-color="%23d4af37"/><stop offset="100%" stop-color="%23b38728"/></linearGradient></defs><path d="M20 50 Q150 10 300 35 Q450 10 580 50" fill="none" stroke="url(%23gold)" stroke-width="4" stroke-linecap="round"/><path d="M292 24 L300 10 L308 24 L314 20 L310 30 L316 32 L308 34 L310 40 L300 36 L290 40 L292 34 L284 32 L290 30 L286 20 Z" fill="url(%23gold)"/></svg>');
}
.card::after{
  bottom:-20px;
  transform:translateX(-50%) rotate(180deg);
  background-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 70"><defs><linearGradient id="gold" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="%23f8e7a1"/><stop offset="50%" stop-color="%23d4af37"/><stop offset="100%" stop-color="%23b38728"/></linearGradient></defs><path d="M20 50 Q150 10 300 35 Q450 10 580 50" fill="none" stroke="url(%23gold)" stroke-width="4" stroke-linecap="round"/><path d="M292 24 L300 10 L308 24 L314 20 L310 30 L316 32 L308 34 L310 40 L300 36 L290 40 L292 34 L284 32 L290 30 L286 20 Z" fill="url(%23gold)"/></svg>');
}

/* TEXT */
h1{
  font-family:"Cinzel Decorative",serif;
  font-size:26px;
  letter-spacing:1px;
  color:#b18a2e;
  margin-bottom:12px;
}
p{
  color:#666;
  font-size:15px;
  line-height:1.6;
  margin-bottom:28px;
}

/* BUTTON */
.btn{
  position:relative;
  background:linear-gradient(120deg,var(--gold1),var(--gold2),#f3d27b);
  border:none;
  border-radius:40px;
  padding:14px 36px;
  font-weight:600;
  font-size:16px;
  color:#3d2b04;
  cursor:pointer;
  overflow:hidden;
  box-shadow:0 8px 22px rgba(212,175,55,.35);
  transition:.3s;
}
.btn:hover{transform:translateY(-3px)}
.btn::after{
  content:"";
  position:absolute;top:0;left:-75%;
  width:50%;height:100%;
  background:linear-gradient(120deg,rgba(255,255,255,.5),rgba(255,255,255,0));
  transform:skewX(-25deg);
  animation:glow 4s infinite;
}

/* POPUP */
.overlay{
  position:fixed;inset:0;
  background:rgba(0,0,0,.45);
  backdrop-filter:blur(4px);
  display:none;
  align-items:center;
  justify-content:center;
  z-index:50;
}
.overlay.show{display:flex}
.popup{
  background:rgba(255,255,255,.9);
  border:1px solid rgba(212,175,55,.25);
  border-radius:20px;
  padding:32px 26px 36px;
  box-shadow:0 10px 35px var(--shadow);
  text-align:center;
  width:90%;
  max-width:350px;
  animation:fadeIn .4s ease;
}
.popup h2{
  font-family:"Playfair Display",serif;
  font-size:22px;
  color:#b18a2e;
  margin-bottom:10px;
}
.popup p{font-size:15px;color:#555;margin-bottom:18px}
.acc{
  background:#fff;
  border:1px solid rgba(212,175,55,.3);
  border-radius:12px;
  padding:12px;
  font-weight:600;
  font-size:15px;
  margin-bottom:18px;
  box-shadow:inset 0 0 8px rgba(212,175,55,.25);
}
.copy{
  background:linear-gradient(120deg,var(--gold1),var(--gold2));
  color:#3d2b04;
  border:none;
  border-radius:12px;
  padding:10px 22px;
  font-weight:600;
  cursor:pointer;
  transition:.25s;
}
.copy:hover{transform:translateY(-2px)}

/* TOAST */
.toast{
  position:fixed;
  left:50%;transform:translateX(-50%);
  bottom:30px;
  background:#3d2b04;color:#fff;
  padding:10px 18px;border-radius:10px;
  font-size:14px;opacity:.9;
  z-index:100;
}

/* ANIMATIONS */
@keyframes glow{
  0%{left:-75%}50%{left:125%}100%{left:125%}
}
@keyframes fadeIn{
  from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
    
</style>
</head>
<script>
window.addEventListener("load", () => {
  fetch("scroll_flag.php")
    .then(res => res.text())
    .then(flag => {
      if (flag.trim() === "1") {
        const modal = document.querySelector("#modalx");
        if (modal) {
          // Tambahkan efek fade-out pada modal
          modal.classList.add("fade-out");

          // Setelah animasi selesai, baru sembunyikan dan scroll
          setTimeout(() => {
            modal.style.display = "none";
            document.body.style.overflow = "auto";

            const el = document.querySelector("#ucapan");
            if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
          }, 700); // durasi animasi + sedikit jeda
        } else {
          // kalau tidak ada modal, langsung scroll saja
          const el = document.querySelector("#ucapan");
          if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      }
    });
});
    </script>
    
<body>
<!-- Modal -->
<div class="modalx" id="modalx" data-sampul="<?= $data['sampul'] ?>">
  <div class="overlayy"></div>
  <div class="content-modalx animate__animated animate__fadeInDown">
    <h1 style="color: #f0e8df;">THE WEDDING</h1>
    <div class="mempelai"><?= htmlspecialchars($data['pria']['nama']) ?> &amp; <?= htmlspecialchars($data['wanita']['nama']) ?></div>
    <div class="tgl"><?= htmlspecialchars($data['akad']['tanggal']) ?></div>
    <div class="wdp-dear">Kpd Bpk/Ibu/Saudara/i</div>
    <div class="wdp-name namatamu" style="margin-top:8px;font-weight:600"><?= $tamu ?></div>
    <div class="wdp-button-wrapper" id="wdp-button-wrapper">
      <button id="openBtn"><span style="margin-right:8px">📩</span> Buka Undangan</button>
    </div>
    <div class="wdp-keterangan">Mohon maaf apabila ada kesalahan penulisan nama/gelar</div>
  </div>
</div>

<!-- Audio -->
<audio id="song" preload="auto">
  <source src="<?= $data['lagu'] ?>" type="audio/mp3">
</audio>

<!-- HERO -->
<header class="hero" aria-label="Hero" data-aos="zoom-in">
  <div class="hero-text">
      <h2>The Wedding of </h2>
      <h3><span style="color:var(--accent)"><?= htmlspecialchars($data['pria']['nama']) ?> &amp; <?= htmlspecialchars($data['wanita']['nama']) ?></span></h3>
    <p><?= htmlspecialchars($data['akad']['tanggal']) ?></p>
  </div>
</header>
  <main class="cont">
    <section class="countdown" id="countdown">
      <div class="count-box">
        <div class="count-value" id="days">00</div>
        <div class="count-label">Hari</div>
      </div>
      <div class="count-box">
        <div class="count-value" id="hours">00</div>
        <div class="count-label">Jam</div>
      </div>
      <div class="count-box">
        <div class="count-value" id="minutes">00</div>
        <div class="count-label">Menit</div>
      </div>
      <div class="count-box">
        <div class="count-value" id="seconds">00</div>
        <div class="count-label">Detik</div>
      </div>
    </section>
    </main>
    
<section class="section" aria-label="Intro" data-aos="fade-up">
  <p style="text-align:center; font-family: Playfair Display, serif; font-size:20px; margin-top:0">
  <?= htmlspecialchars($data['kata_mutiara']) ?></p>
</section>

 <h2 class="judul-berbahagia">Kami Yang Berbahagia :</h2>
<!-- PASANGAN -->
<section class="pair" aria-label="Mempelai">
  <div class="card-arch" data-aos="fade-right">
    <div class="photo-wrap">
      <img src="<?= $data['pria']['foto'] ?>">
    </div>
    <h3><?= htmlspecialchars($data['pria']['nama']) ?></h3>
    <div class="keluarga-divider"></div>
    <div class="keluarga-names">
      <span> <?= htmlspecialchars($data['keluarga_pria']['teks']) ?> </span>
    </div>
  </div>

  <div class="card-arch" data-aos="fade-left">
    <div class="photo-wrap">
      <img src="<?= $data['wanita']['foto'] ?>">
    </div>
    <h3><?= htmlspecialchars($data['wanita']['nama']) ?></h3>
    <div class="keluarga-divider"></div>
    <div class="keluarga-names">
      <span> <?= htmlspecialchars($data['keluarga_wanita']['teks']) ?> </span>
    </div>
     
  </div>
</section>
 <h2 class="judul-gallery">Our Gallery</h2>      
<section class="gallery" aria-label="Gallery">
  <?php foreach ($data['galeri'] as $g): if ($g): ?>
  <img data-aos="fade-left" src="<?= $g ?>" alt="Gallery">
<?php endif; endforeach; ?>
</section>
<!-- EVENTS & MAP -->
<section class="events" aria-label="Events & Map">
 <h2 data-aos="fade-up">Events & Map</h2>

  <div class="columns">
    <div class="column" data-aos="fade-right">
      <h3>Akad</h3>
      <p><?= htmlspecialchars($data['akad']['tanggal']) ?></p>
      <p><?= htmlspecialchars($data['akad']['waktu']) ?></p>
    </div>
    <div class="column" data-aos="fade-left">
      <h3>Resepsi</h3>
      <p><?= htmlspecialchars($data['resepsi']['tanggal']) ?></p>
      <p><?= htmlspecialchars($data['resepsi']['waktu']) ?></p>
    </div>
  </div>

  <div class="address" data-aos="fade-up">
<strong><?= htmlspecialchars($data['tempat']['nama']) ?>:</strong><br>
    <?= htmlspecialchars($data['tempat']['alamat']) ?>
  </div>

  <div class="map" data-aos="zoom-in">
    <iframe src="<?= $data['tempat']['maps'] ?>" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
  </div>
</section>
<!-- KELUARGA -->
<section class="keluarga-section" aria-label="Keluarga">

  <h2>Turut Mengundang</h2>
  <p class="subtitle">Keluarga besar dari kedua mempelai</p>

  <!-- Pria -->
  <div class="keluarga-block">
    <div class="keluarga-title">Keluarga Mempelai Pria</div>
    <div class="keluarga-divider"></div>
    <div class="keluarga-names">
        <span><?= htmlspecialchars($data['keluarga_pria']['keterangan4']) ?></span>
        <span><?= htmlspecialchars($data['keluarga_pria']['keterangan1']) ?></span>
        <span><?= htmlspecialchars($data['keluarga_pria']['keterangan2']) ?></span>
        <span><?= htmlspecialchars($data['keluarga_pria']['keterangan3']) ?></span>
    </div>
  </div>

  <!-- Wanita -->
  <div class="keluarga-block">
    <div class="keluarga-title">Keluarga Mempelai Wanita</div>
    <div class="keluarga-divider"></div>
    <div class="keluarga-names">
      <span><?= htmlspecialchars($data['keluarga_wanita']['keterangan4']) ?>
      <span><?= htmlspecialchars($data['keluarga_wanita']['keterangan1']) ?>
      <span><?= htmlspecialchars($data['keluarga_wanita']['keterangan2']) ?>
      <span><?= htmlspecialchars($data['keluarga_wanita']['keterangan3']) ?>
        </span>
    </div>
  </div>

</section>

<!-- UCAPAN KEHORMATAN & DOA RESTU -->
<section class="doa-restu-section" style="background:#a67c74;padding:60px 20px;text-align:center;color:#fff;" data-aos="fade-up">
  <div style="max-width:800px;margin:auto;">
    <p style="font-size:1.1rem;line-height:1.8;margin-bottom:25px;font-family:'Playfair Display',serif;">
      Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i,
      berkenan hadir dan memberikan doa restu kepada kedua mempelai.
    </p>
    <p style="font-size:1.1rem;line-height:1.8;font-family:'Playfair Display',serif;">
      Atas kehadiran serta doa restu Bapak/Ibu/Saudara/i, kami ucapkan terima kasih.
      <br>Wassalamualaikum Wr. Wb.
    </p>
  </div>
</section>
 
    <div class="contan" data-aos="fade-up" data-aos-duration="1500">
  <header>
    <div class="dec">
      <div class="line"></div>
      <div class="heart">❤</div>
      <div class="line"></div>
    </div>
    <h1>KIRIMKAN UCAPAN & DOA</h1>
  </header>

  <form id="commentForm" method="POST">
    <label>Nama</label>
    <input type="text" name="name" required placeholder="Isikan Nama Anda" />
    <label>Pesan</label>
    <textarea name="msg" required placeholder="Berikan Ucapan dan Doa Restu"></textarea>
    <label>Konfirmasi Kehadiran</label>
    <input id="confirm" name="confirm" readonly class="selectbox" value="Konfirmasi Kehadiran" onclick="openModal()" />
    <button class="btn" type="submit">Kirimkan Ucapan</button>
  </form>

  <!-- Divider -->
  <hr style="border:0;height:2px;width:100%;background:linear-gradient(to right,#f6b319,#dcb75e,#f6b319);border-radius:2px;margin:40px 0;">

  <div class="messages" id="ucapan">
    <?php foreach ($list as $item): ?>
      <div class="msg">
        <div class="avatar"><?= $item['initial'] ?></div>
        <div>
          <b><?= $item['name'] ?></b>
          <span class="badge"><?= $item['icon'] ?> <?= $item['confirm'] ?></span><br>
          <?= htmlspecialchars($item['msg']) ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Divider -->
  <hr style="border:0;height:2px;width:100%;background:linear-gradient(to right,#f6b319,#dcb75e,#f6b319);border-radius:2px;margin:40px 0;">

  <!-- Modal -->
  <div class="modal-backdrop" id="backdrop">
    <div class="modal" role="dialog" aria-modal="true">
      <div style="padding:18px 20px;font-size:20px;font-weight:700;border-bottom:1px solid rgba(0,0,0,0.05)">
        Konfirmasi Kehadiran
      </div>
      <div class="opt" data-value="Hadir" onclick="selectOpt(this)">
        <div>Hadir</div><span class="radio" id="r-hadir"></span>
      </div>
      <div class="opt" data-value="Akan Hadir" onclick="selectOpt(this)">
        <div>Akan Hadir</div><span class="radio" id="r-akan"></span>
      </div>
      <div class="opt" data-value="Tidak Hadir" onclick="selectOpt(this)">
        <div>Tidak Hadir</div><span class="radio" id="r-tidak"></span>
      </div>
    </div>
  </div>
</div> <!-- tutup .contan -->
    
    
    <section id="cashless">
<div class="card">
  <h1> Hadiah </h1>
  <p>Bagi keluarga dan sahabat yang ingin memberikan hadiah,  
  silakan klik tombol di bawah. Terima kasih atas doa serta dukungan Anda 💛</p>
  <button class="btn" id="open"> Beri Hadiah</button>
</div>

<div class="overlay" id="overlay">
  <div class="popup">
    <h2>Terima Kasih 💛</h2>
    <p>Doa dan hadiah terbaik Anda sangat berarti bagi kami.</p>
    <div class="acc" id="accnum"><?= htmlspecialchars($data['bank']) ?></div>
    <button class="copy" id="copy">Salin Nomor</button>
  </div>
    </div>
    </section>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    
<section class="thankyou-section" style="position:relative;text-align:center;color:#fff;">
  <!-- Box Kutipan Ayat -->
  <div style="background:#fae4d3;padding:30px 20px;">
    <div data-aos="fade-up" data-aos-duration="1600" style="max-width:700px;margin:auto;background:#f6d1ad;padding:25px;border-radius:15px;box-shadow:0 4px 10px rgba(0,0,0,0.1);">
      <p style="font-style:italic;color:#444;font-size:1.05rem;line-height:1.8;margin-bottom:10px;">
         <?= nl2br(htmlspecialchars($data['doa']['teks'])) ?> 
     </p>
    </div>
  </div>
</section>
      <footer data-aos="fade-up">
  <div>Terima kasih atas doa dan kehadiran Anda</div>
      </footer>
      
<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    once: true,        // animasi hanya jalan sekali
    duration: 1200,    // durasi default 1.2 detik
    offset: 100        // jarak sebelum elemen muncul
  });
</script>
<!-- SCRIPT -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
(function(){
  const modal = document.getElementById('modalx');
  const sampul = modal && modal.dataset.sampul;
  if(sampul) modal.style.backgroundImage = 'url(' + sampul + ')';
  document.body.style.overflow = 'hidden';
  if(window.AOS) AOS.init({ duration:1000, once:true, offset:80, easing:'ease-out-cubic' });

  document.getElementById('wdp-button-wrapper').addEventListener('click', function(){
    modal.classList.add('animate__animated','animate__zoomOut');
    setTimeout(()=> { modal.style.display='none'; },600);
    document.body.style.overflow='auto';
    const s=document.getElementById('song');
    if(s && s.play) s.play().catch(()=>{});
  });

})();
    const eventDate = new Date("<?= $event_date ?>").getTime();

const timer = setInterval(() => {
  const now = new Date().getTime();
  const diff = eventDate - now;

  if (diff <= 0) {
    document.getElementById("countdown").innerHTML = "<strong>Acara sedang berlangsung!</strong>";
    clearInterval(timer);
    return;
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  document.getElementById("days").textContent = String(days).padStart(2, '0');
  document.getElementById("hours").textContent = String(hours).padStart(2, '0');
  document.getElementById("minutes").textContent = String(minutes).padStart(2, '0');
  document.getElementById("seconds").textContent = String(seconds).padStart(2, '0');
}, 1000);
    
        const backdrop=document.getElementById('backdrop');
    const selectInput=document.getElementById('confirm');
    function openModal(){backdrop.style.display='flex';}
    function selectOpt(el){
      document.querySelectorAll('.radio').forEach(r=>r.classList.remove('selected'));
      el.querySelector('.radio').classList.add('selected');
      selectInput.value=el.getAttribute('data-value');
      setTimeout(()=>backdrop.style.display='none',180);
    }
    backdrop.addEventListener('click',e=>{if(e.target===backdrop)backdrop.style.display='none'});

    const open=document.getElementById("open");
const overlay=document.getElementById("overlay");
const copyBtn=document.getElementById("copy");
const acc=document.getElementById("accnum");

open.onclick=()=>overlay.classList.add("show");
overlay.onclick=e=>{if(e.target===overlay)overlay.classList.remove("show")};

copyBtn.onclick=()=>{
  navigator.clipboard.writeText(acc.textContent)
    .then(()=>showToast("Nomor rekening tersalin"))
    .catch(()=>showToast("Gagal menyalin"));
};

function showToast(msg){
  const t=document.createElement("div");
  t.className="toast";t.textContent=msg;
  document.body.appendChild(t);
  setTimeout(()=>t.remove(),1800);
}
    </script>
</body>
</html