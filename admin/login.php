<?php
ini_set('session.save_path', '/tmp');
ini_set('session.cookie_path', '/');
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: /admin/');
    exit;
}

require_once __DIR__ . '/../config/db.php';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $stmt  = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($senha, $admin['senha'])) {
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        header('Location: /admin/');
        exit;
    }
    $erro = 'E-mail ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — Login</title>
  <link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <span>Tamires Müller</span>
      <small>Painel administrativo</small>
    </div>
    <?php if ($erro): ?>
      <p class="alert alert--erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <form method="POST" class="login-form">
      <div class="campo">
        <label>E-mail</label>
        <input type="email" name="email" required autofocus
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="campo">
        <label>Senha</label>
        <input type="password" name="senha" required>
      </div>
      <button type="submit" class="btn-admin btn--verde">Entrar</button>
    </form>
  </div>
</body>
</html>