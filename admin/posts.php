<?php
require_once __DIR__ . '/admin-auth.php';
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([(int)$_GET['deletar']]);
    header('Location: /admin/posts.php?msg=deletado');
    exit;
}
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE posts SET publicado = NOT publicado WHERE id = ?")->execute([(int)$_GET['toggle']]);
    header('Location: /admin/posts.php?msg=atualizado');
    exit;
}

$posts = $pdo->query("SELECT * FROM posts ORDER BY publicado_em DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Posts — Admin</title>
  <link rel="stylesheet" href="/admin/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">
    <div class="topbar">
      <h1>Posts do blog</h1>
      <a href="/admin/post-novo.php" class="btn-admin btn--verde">+ Novo post</a>
    </div>
    <div class="content">

      <?php if (isset($_GET['msg'])): ?>
        <?php $msgs = ['deletado'=>['erro','Post deletado.'],'criado'=>['sucesso','Post criado com sucesso.'],'atualizado'=>['sucesso','Post atualizado.'],'nao-encontrado'=>['erro','Post não encontrado.']]; ?>
        <?php [$tipo,$texto] = $msgs[$_GET['msg']] ?? ['sucesso',$_GET['msg']]; ?>
        <div class="alert alert--<?= $tipo ?>" id="alerta"><?= $texto ?></div>
      <?php endif; ?>

      <div class="card">
        <?php if (!$posts): ?>
          <div style="padding:3rem;text-align:center;color:var(--atxt2);">
            Nenhum post ainda. <a href="/admin/post-novo.php" style="color:var(--av2);">Criar o primeiro</a>
          </div>
        <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>Título</th><th>Categoria</th><th>Data</th>
              <th>Status</th><th style="text-align:right;">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($posts as $p):
              $cats = ['manejo'=>'Manejo','gestao'=>'Gestão','saude'=>'Saúde animal','senar'=>'SENAR'];
            ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                <div style="font-size:.72rem;color:var(--atxt2);margin-top:.15rem;">
                  /post/<?= htmlspecialchars($p['slug']) ?>
                </div>
              </td>
              <td><?= htmlspecialchars($cats[$p['categoria']] ?? ucfirst($p['categoria'])) ?></td>
              <td><?= date('d/m/Y', strtotime($p['publicado_em'])) ?></td>
              <td>
                <button onclick="confirmarToggle(<?= $p['id'] ?>, <?= $p['publicado'] ? 1 : 0 ?>)"
                        class="badge <?= $p['publicado'] ? 'badge--verde' : 'badge--cinza' ?>"
                        style="cursor:pointer;border:none;">
                  <?= $p['publicado'] ? 'Publicado' : 'Rascunho' ?>
                </button>
              </td>
              <td>
                <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                  <a href="/admin/post-editar.php?id=<?= $p['id'] ?>"
                     class="btn-admin btn--outline btn--sm">Editar</a>
                  <a href="/post/<?= $p['slug'] ?>" target="_blank"
                     class="btn-admin btn--outline btn--sm">Ver ↗</a>
                  <button onclick="confirmarDelete(<?= $p['id'] ?>, '<?= addslashes(htmlspecialchars($p['titulo'])) ?>')"
                          class="btn-admin btn--perigo btn--sm">Deletar</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<!-- MODAL DELETE -->
<div id="modal-delete" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);
     z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:2rem;max-width:420px;width:90%;text-align:center;">
    <div style="width:52px;height:52px;background:#FEE2E2;border-radius:50%;
                display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#991B1B" stroke-width="2" style="width:24px;">
        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
        <path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
      </svg>
    </div>
    <h3 style="font-size:1.1rem;margin-bottom:.4rem;">Deletar este post?</h3>
    <p id="modal-delete-titulo" style="font-size:.85rem;color:#666;margin-bottom:.5rem;"></p>
    <p style="font-size:.8rem;color:#999;margin-bottom:1.5rem;">Esta ação não pode ser desfeita.</p>
    <div style="display:flex;gap:.75rem;justify-content:center;">
      <button onclick="fecharModal('delete')" class="btn-admin btn--outline">Cancelar</button>
      <a id="link-delete" href="#" class="btn-admin btn--perigo">Sim, deletar</a>
    </div>
  </div>
</div>

<!-- MODAL TOGGLE STATUS -->
<div id="modal-toggle" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);
     z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:2rem;max-width:380px;width:90%;text-align:center;">
    <div style="width:52px;height:52px;background:#D1FAE5;border-radius:50%;
                display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#065F46" stroke-width="2" style="width:24px;">
        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
      </svg>
    </div>
    <h3 id="modal-toggle-titulo" style="font-size:1rem;margin-bottom:.5rem;"></h3>
    <p style="font-size:.82rem;color:#666;margin-bottom:1.5rem;">Confirme a alteração de status.</p>
    <div style="display:flex;gap:.75rem;justify-content:center;">
      <button onclick="fecharModal('toggle')" class="btn-admin btn--outline">Cancelar</button>
      <a id="link-toggle" href="#" class="btn-admin btn--verde">Confirmar</a>
    </div>
  </div>
</div>

<script>
function confirmarDelete(id, titulo) {
  document.getElementById('modal-delete-titulo').textContent = '"' + titulo + '"';
  document.getElementById('link-delete').href = '?deletar=' + id;
  document.getElementById('modal-delete').style.display = 'flex';
}
function confirmarToggle(id, publicado) {
  const acao = publicado ? 'despublicar' : 'publicar';
  document.getElementById('modal-toggle-titulo').textContent =
    'Deseja ' + acao + ' este post?';
  document.getElementById('link-toggle').href = '?toggle=' + id;
  document.getElementById('modal-toggle').style.display = 'flex';
}
function fecharModal(tipo) {
  document.getElementById('modal-' + tipo).style.display = 'none';
}
['modal-delete','modal-toggle'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
  });
});
const alerta = document.getElementById('alerta');
if (alerta) setTimeout(() => { alerta.style.transition='opacity .5s'; alerta.style.opacity='0'; }, 3500);
</script>
</body>
</html>