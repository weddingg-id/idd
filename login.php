<?php
session_start();

// Jika sudah login, langsung arahkan ke admin
if (!empty($_SESSION['is_admin'])) {
    header('Location: admin.php');
    exit;
}

// Buat token CSRF jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Token tidak valid, silakan muat ulang halaman.';
    } else {
        $password = trim($_POST['password'] ?? '');
        $valid_password = 'elawww'; // password yang kamu minta

        if (hash_equals($valid_password, $password)) {
            session_regenerate_id(true);
            $_SESSION['is_admin'] = true;
            unset($_SESSION['csrf_token']);
            header('Location: admin.php'); // ubah jika mau redirect ke halaman lain
            exit;
        } else {
            $error = 'Password salah.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Login Admin</title>
<style>
  body{font-family:Rubik,Arial,Helvetica,sans-serif;background:#f6f6f6;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
  .card{background:#fff;padding:28px;border-radius:12px;box-shadow:0 6px 24px rgba(0,0,0,0.08);width:340px;text-align:center}
  h2{margin:0 0 16px;font-family:Playfair Display,serif;color:#333}
  input[type=password]{width:100%;padding:10px;border-radius:8px;border:1px solid #ddd;box-sizing:border-box;font-size:15px}
  button{margin-top:16px;width:100%;padding:10px;border-radius:8px;border:0;background:#b1744a;color:#fff;font-weight:700;cursor:pointer}
  button:hover{background:#a2683e}
  .err{background:#ffe6e6;color:#8b0000;padding:10px;border-radius:8px;margin-bottom:12px}
  .small{font-size:13px;color:#666;margin-top:8px;text-align:center}
</style>
</head>
<body>
  <div class="card">
    <h2>Login Admin</h2>
    <?php if ($error): ?>
      <div class="err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="password" name="password" placeholder="Masukkan password" required>
      <button type="submit">Masuk</button>
    </form>
  </div>
</body>
</html>
