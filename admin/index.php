<?php
require_once __DIR__ . '/admin-auth.php';
require_once __DIR__ . '/../config/db.php';

$total_contatos    = $pdo->query("SELECT COUNT(*) FROM contatos")->fetchColumn();
$novos_contatos    = $pdo->query("SELECT COUNT(*) FROM contatos WHERE lido = 0")->fetchColumn();
$total_posts       = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_newsletter  = $pdo->query("SELECT COUNT(*) FROM newsletter WHERE ativo = 1")->fetchColumn();

$ultimos_contatos  = $pdo->query(
  "SELECT * FROM contatos ORDER BY criado_em DESC LIMIT 6"
)->fetchAll();

$intencao_labels = [
  'clinica' => ['Clínica', 'badge--azul'],
  'consultoria' => ['Consultoria', 'badge--verde'],
  'senar' => ['SENAR', 'badge--laranja'],
  'outro' => ['Outro', 'badge--cinza'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Admin</title>
  <link rel="stylesheet" href="/admin/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">
    <div class="topbar">
      <h1>Dashboard</h1>
      <div class="topbar__user">
        Olá, <?= htmlspecialchars($_SESSION['admin_nome']) ?>
        <a href="/admin/logout.php" class="btn-admin btn--outline btn--sm">Sair</a>
      </div>
    </div>
    <div class="content">
      <div class="metricas">
        <div class="metrica-card">
          <div class="metrica-card__label">Contatos totais</div>
          <div class="metrica-card__valor"><?= $total_contatos ?></div>
          <div class="metrica-card__sub"><?= $novos_contatos ?> não lidos</div>
        </div>
        <div class="metrica-card">
          <div class="metrica-card__label">Posts publicados</div>
          <div class="metrica-card__valor"><?= $total_posts ?></div>
          <div class="metrica-card__sub">no blog</div>
        </div>
        <div class="metrica-card">
          <div class="metrica-card__label">Newsletter</div>
          <div class="metrica-card__valor"><?= $total_newsletter ?></div>
          <div class="metrica-card__sub">e-mails cadastrados</div>
        </div>
      </div>

      <div class="card">
        <div class="card__header">
          <h2>Últimas solicitações</h2>
          <a href="/admin/contatos.php" class="btn-admin btn--outline btn--sm">Ver todos</a>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Nome</th><th>Telefone</th><th>Tipo de criação</th>
              <th>Intenção</th><th>Data</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ultimos_contatos as $c):
              [$label, $classe] = $intencao_labels[$c['intencao']] ?? ['Outro', 'badge--cinza'];
            ?>
            <tr>
              <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
              <td><?= htmlspecialchars($c['telefone']) ?></td>
              <td><?= htmlspecialchars($c['tipo_criacao'] ?: '—') ?></td>
              <td><span class="badge <?= $classe ?>"><?= $label ?></span></td>
              <td><?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?></td>
              <td>
                <span class="badge <?= $c['lido'] ? 'badge--cinza' : 'badge--verde' ?>">
                  <?= $c['lido'] ? 'Lido' : 'Novo' ?>
                </span>
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

