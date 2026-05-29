<?php
require_once __DIR__ . '/admin-auth.php';
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['remover'])) {
    $pdo->prepare("UPDATE newsletter SET ativo = 0 WHERE id = ?")->execute([(int)$_GET['remover']]);
    header('Location: /admin/newsletter.php');
    exit;
}

$lista = $pdo->query("SELECT * FROM newsletter ORDER BY criado_em DESC")->fetchAll();
$total = $pdo->query("SELECT COUNT(*) FROM newsletter WHERE ativo = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Newsletter — Admin</title>
  <link rel="stylesheet" href="/admin/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">
    <div class="topbar">
      <h1>Newsletter</h1>
      <span class="badge badge--verde" style="font-size:.85rem;padding:.35rem .875rem;"><?= $total ?> ativos</span>
    </div>
    <div class="content">
      <div class="card">
        <table class="table">
          <thead><tr><th>E-mail</th><th>Cadastrado em</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($lista as $n): ?>
            <tr>
              <td><?= htmlspecialchars($n['email']) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($n['criado_em'])) ?></td>
              <td><span class="badge <?= $n['ativo'] ? 'badge--verde' : 'badge--cinza' ?>"><?= $n['ativo'] ? 'Ativo' : 'Inativo' ?></span></td>
              <td>
                <?php if ($n['ativo']): ?>
                  <a href="?remover=<?= $n['id'] ?>" class="btn-admin btn--perigo btn--sm"
                     onclick="return confirm('Remover este e-mail?')">Remover</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>