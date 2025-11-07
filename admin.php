<?php
session_start();
if (empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

// ----------------------------------------
// Konfigurasi dasar
// ----------------------------------------
$data_file = "data.txt";
$upload_dir = "uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// ----------------------------------------
// Fungsi Upload File
// ----------------------------------------
function uploadFile($input_name, $old_file = null) {
    global $upload_dir;
    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === 0) {
        $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
        $new_name = uniqid() . '.' . strtolower($ext);
        $target = $upload_dir . $new_name;

        if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target)) {
            if ($old_file && file_exists($old_file)) unlink($old_file);
            return $target;
        }
    }
    return $old_file;
}

// ----------------------------------------
// Baca data lama (jika ada)
// ----------------------------------------
$data = [];
if (file_exists($data_file)) {
    $data = json_decode(file_get_contents($data_file), true);
}

// ----------------------------------------
// Tangani Reset atau Simpan
// ----------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -------- Reset Data --------
    if (isset($_POST['reset_data'])) {
        // Kosongkan data.txt
        file_put_contents($data_file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Hapus semua file di folder uploads
        $files = glob($upload_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }

        $data = [];
        $message = "<div style='background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin:10px 0;'>✅ Semua data berhasil dihapus!</div>";

    // -------- Simpan Data --------
    } else {
        $data['judul'] = $_POST['judul'] ?? '';
        $data['bank'] = $_POST['bank'] ?? '';

        $data['pria'] = [
            'nama' => $_POST['pria_nama'] ?? '',
            'foto' => uploadFile('pria_foto', $data['pria']['foto'] ?? null)
        ];

        $data['wanita'] = [
            'nama' => $_POST['wanita_nama'] ?? '',
            'foto' => uploadFile('wanita_foto', $data['wanita']['foto'] ?? null)
        ];

        $data['akad'] = [
            'tanggal' => $_POST['akad_tanggal'] ?? '',
            'waktu' => $_POST['akad_waktu'] ?? ''
        ];
        
        $data['countdown'] = [
    'tanggal' => $_POST['countdown_tanggal'] ?? '',
    'waktu' => $_POST['countdown_waktu'] ?? ''
];
        

        $data['resepsi'] = [
            'tanggal' => $_POST['resepsi_tanggal'] ?? '',
            'waktu' => $_POST['resepsi_waktu'] ?? ''
        ];

        $data['tempat'] = [
            'nama' => $_POST['tempat_nama'] ?? '',
            'alamat' => $_POST['tempat_alamat'] ?? '',
            'maps' => $_POST['maps'] ?? ''
        ];

        $data['lagu'] = uploadFile('lagu', $data['lagu'] ?? null);
        $data['sampul'] = uploadFile('sampul', $data['sampul'] ?? null);

        $data['galeri'] = [
            uploadFile('galeri1', $data['galeri'][0] ?? null),
            uploadFile('galeri2', $data['galeri'][1] ?? null),
            uploadFile('galeri3', $data['galeri'][2] ?? null),
            uploadFile('galeri4', $data['galeri'][3] ?? null)
        ];

        $data['keluarga_pria'] = [
            'foto' => uploadFile('kel_pria_foto', $data['keluarga_pria']['foto'] ?? null),
            'teks' => $_POST['kel_pria_teks'] ?? '',
            'keterangan4' => $_POST['kel_pria_ket4'] ?? '',
            'keterangan1' => $_POST['kel_pria_ket1'] ?? '',
            'keterangan2' => $_POST['kel_pria_ket2'] ?? '',
            'keterangan3' => $_POST['kel_pria_ket3'] ?? ''
        ];

        $data['keluarga_wanita'] = [
            'foto' => uploadFile('kel_wanita_foto', $data['keluarga_wanita']['foto'] ?? null),
            'teks' => $_POST['kel_wanita_teks'] ?? '',
            'keterangan4' => $_POST['kel_wanita_ket4'] ?? '',
            'keterangan1' => $_POST['kel_wanita_ket1'] ?? '',
            'keterangan2' => $_POST['kel_wanita_ket2'] ?? '',
            'keterangan3' => $_POST['kel_wanita_ket3'] ?? '',
        ];

        $data['kata_mutiara'] = $_POST['kata_mutiara'] ?? '';
        $data['foto_terimakasih'] = uploadFile('foto_terimakasih', $data['foto_terimakasih'] ?? null);

        $data['doa'] = [
            'teks' => $_POST['doa_teks'] ?? '',
            'sumber' => $_POST['doa_sumber'] ?? ''
        ];

        file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $message = "<div style='background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin:10px 0;'>✅ Data berhasil disimpan!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin Undangan Digital</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f4efe8; padding:20px; }
form { background:#fff; padding:20px 30px; border-radius:10px; max-width:900px; margin:auto; box-shadow:0 3px 10px rgba(0,0,0,0.1); }
h2 { color:#7a4b28; border-bottom:1px solid #ddd; padding-bottom:4px; margin-top:30px; }
label { font-weight:600; display:block; margin-top:10px; }
input[type=text], textarea { width:100%; padding:8px; margin-top:4px; border:1px solid #ccc; border-radius:5px; }
input[type=file] { margin-top:5px; }
button { background:#7a4b28; color:#fff; border:0; padding:10px 20px; border-radius:5px; margin-top:20px; cursor:pointer; }
button.reset { background:#a94442; margin-left:10px; }
.preview { display:block; margin-top:5px; max-height:100px; border-radius:6px; }
</style>
</head>
<body>

<h1 style="text-align:center;color:#7a4b28;">🕊️ Admin Undangan Digital</h1>

<?= $message ?? '' ?>

<form method="POST" enctype="multipart/form-data" onsubmit="return confirmReset(event);">
  <!-- Informasi Umum -->
  <h2>Informasi Umum</h2>
  <label>Judul Undangan:</label>
  <input type="text" name="judul" value="<?= $data['judul'] ?? '' ?>" required>

  <!-- Mempelai -->
  <h2>Mempelai</h2>
  <label>Nama Mempelai Pria:</label>
  <input type="text" name="pria_nama" value="<?= $data['pria']['nama'] ?? '' ?>" required>
  <label>Foto Mempelai Pria:</label>
  <input type="file" name="pria_foto">
  <?php if (!empty($data['pria']['foto'])) echo "<img src='{$data['pria']['foto']}' class='preview'>"; ?>

  <label>Nama Mempelai Wanita:</label>
  <input type="text" name="wanita_nama" value="<?= $data['wanita']['nama'] ?? '' ?>" required>
  <label>Foto Mempelai Wanita:</label>
  <input type="file" name="wanita_foto">
  <?php if (!empty($data['wanita']['foto'])) echo "<img src='{$data['wanita']['foto']}' class='preview'>"; ?>

    <!-- Countdown -->
<h2>Countdown</h2>
    <label>Judul Countdown:</label>
<label>Tanggal Countdown:</label>
<input type="date" name="countdown_tanggal" value="<?= $data['countdown']['tanggal'] ?? '' ?>">

<label>Waktu Countdown:</label>
<input type="time" name="countdown_waktu" value="<?= $data['countdown']['waktu'] ?? '' ?>">
    
  <!-- Acara -->
  <h2>Acara</h2>
  <label>Tanggal & Waktu Akad:</label>
  <input type="text" name="akad_tanggal" placeholder="Contoh: Sabtu, 29 Oktober 2022" value="<?= $data['akad']['tanggal'] ?? '' ?>">
  <input type="text" name="akad_waktu" placeholder="08.00 WIB" value="<?= $data['akad']['waktu'] ?? '' ?>">

  <label>Tanggal & Waktu Resepsi:</label>
  <input type="text" name="resepsi_tanggal" placeholder="Sabtu, 29 Oktober 2022" value="<?= $data['resepsi']['tanggal'] ?? '' ?>">
  <input type="text" name="resepsi_waktu" placeholder="11.00 - 14.00 WIB" value="<?= $data['resepsi']['waktu'] ?? '' ?>">

  <label>Nama Tempat:</label>
  <input type="text" name="tempat_nama" value="<?= $data['tempat']['nama'] ?? '' ?>">
  <label>Alamat Tempat:</label>
  <textarea name="tempat_alamat"><?= $data['tempat']['alamat'] ?? '' ?></textarea>
  <label>Google Maps Embed:</label>
  <textarea name="maps" rows="2"><?= $data['tempat']['maps'] ?? '' ?></textarea>

  <!-- File Pendukung -->
  <h2>File Pendukung</h2>
  <label>Backsound (MP3):</label>
  <input type="file" name="lagu" accept=".mp3">
  <?php if (!empty($data['lagu'])) echo "<audio controls src='{$data['lagu']}'></audio>"; ?>

  <label>Foto Sampul Modal Pembuka:</label>
  <input type="file" name="sampul">
  <?php if (!empty($data['sampul'])) echo "<img src='{$data['sampul']}' class='preview'>"; ?>

  <!-- Galeri -->
  <h2>Galeri (4 Foto)</h2>
  <?php
  for ($i=1; $i<=4; $i++):
      $img = $data['galeri'][$i-1] ?? '';
      echo "<label>Foto Galeri {$i}:</label><input type='file' name='galeri{$i}'>";
      if ($img) echo "<img src='{$img}' class='preview'>";
  endfor;
  ?>

  <!-- Keluarga -->
  <h2>Keluarga Mempelai Pria</h2>
  <label>Ayah dari mempelai pria:</label>
  <input type="text" name="kel_pria_teks" value="<?= $data['keluarga_pria']['teks'] ?? '' ?>">
  <label>Ibu dari mempelai pria:</label>
  <textarea name="kel_pria_ket4"><?= $data['keluarga_pria']['keterangan4'] ?? '' ?></textarea>
  <textarea name="kel_pria_ket1"><?= $data['keluarga_pria']['keterangan1'] ?? '' ?></textarea>
  <textarea name="kel_pria_ket2"><?= $data['keluarga_pria']['keterangan2'] ?? '' ?></textarea>
  <textarea name="kel_pria_ket3"><?= $data['keluarga_pria']['keterangan3'] ?? '' ?></textarea>

  <h2>Keluarga Mempelai Wanita</h2>
  <label>Ayah dari mempelai wanita:</label>
  <input type="text" name="kel_wanita_teks" value="<?= $data['keluarga_wanita']['teks'] ?? '' ?>">
  <label>Ibu dari mempelai wanita:</label>
  <textarea name="kel_wanita_ket4"><?= $data['keluarga_wanita']['keterangan4'] ?? '' ?></textarea>
  <textarea name="kel_wanita_ket1"><?= $data['keluarga_wanita']['keterangan1'] ?? '' ?></textarea>
  <textarea name="kel_wanita_ket2"><?= $data['keluarga_wanita']['keterangan2'] ?? '' ?></textarea>
  <textarea name="kel_wanita_ket3"><?= $data['keluarga_wanita']['keterangan3'] ?? '' ?></textarea>

  <!-- Kata Mutiara -->
  <h2>Kata Mutiara</h2>
  <textarea name="kata_mutiara" rows="2"><?= $data['kata_mutiara'] ?? '' ?></textarea>

  <!-- Foto Terimakasih -->
  <h2>Foto Terimakasih</h2>
  <input type="file" name="foto_terimakasih">
  <?php if (!empty($data['foto_terimakasih'])) echo "<img src='{$data['foto_terimakasih']}' class='preview'>"; ?>

  <!-- Kutipan Doa -->
  <h2>Kutipan Doa</h2>
  <label>Teks Doa:</label>
  <textarea name="doa_teks"><?= $data['doa']['teks'] ?? '' ?></textarea>
  <label>Sumber:</label>
  <input type="text" name="doa_sumber" value="<?= $data['doa']['sumber'] ?? '' ?>">
  <!-- Rekening Bank -->
<h2>Rekening Bank</h2>
<label for="bank">Nomor / Nama Rekening:</label>
<textarea name="bank" id="bank" rows="2" placeholder="Contoh: BCA 123456789 a/n Nama Anda"><?= $data['bank'] ?? '' ?></textarea>
    
  <!-- Tombol Simpan dan Reset -->
  <button type="submit" name="save_data">💾 Simpan Data</button>
  <button type="submit" name="reset_data" class="reset">🗑️ Reset Semua Data</button>
</form>

<script>
function confirmReset(e) {
    if (e.submitter.name === 'reset_data') {
        return confirm('⚠️ Semua data akan dihapus dan tidak bisa dikembalikan. Lanjutkan?');
    }
    return true;
}
</script>

<!-- Tombol Logout -->
<a href="logout.php" style="
     display:inline-block;
     margin:40px auto;
     text-decoration:none;
     background:#b1744a;
     color:#fff;
     padding:12px 28px;
     border-radius:10px;
     font-family:'Rubik',sans-serif;
     font-weight:600;
     box-shadow:0 8px 20px rgba(177,116,74,0.25);
     transition:all .25s ease;
     text-align:center;
   "
   onmouseover="this.style.transform='scale(1.05)'"
   onmouseout="this.style.transform='scale(1)'">
   🔒 Logout
</a>

</body>
</html>
