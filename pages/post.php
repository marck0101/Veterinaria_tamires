<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$slug = sanitize($_GET['slug'] ?? '');
$post = $slug ? getPostBySlug($pdo, $slug) : null;

if (!$post) {
    http_response_code(404);
    $page_title = 'Post não encontrado';
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="container secao" style="text-align:center;">
      <h1>Post não encontrado</h1>
      <a href="/blog" class="btn btn--primario" style="margin-top:1rem;">← Voltar ao blog</a>
    </div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$page_title = htmlspecialchars($post['titulo']) . ' — Tamires Müller Veterinária';
$page_desc  = htmlspecialchars($post['resumo']);

$categorias_label = [
  'manejo' => 'Manejo', 'gestao' => 'Gestão',
  'saude'  => 'Saúde animal', 'senar' => 'SENAR',
];

// Posts relacionados
$relacionados = $pdo->prepare(
  "SELECT * FROM posts WHERE publicado=1 AND categoria=? AND slug != ? ORDER BY publicado_em DESC LIMIT 3"
);
$relacionados->execute([$post['categoria'], $post['slug']]);
$relacionados = $relacionados->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<!-- BREADCRUMB -->
<div class="container" style="padding-top:1.5rem;">
  <nav style="font-size:.8rem;color:var(--cinza-medio);display:flex;gap:.4rem;align-items:center;">
    <a href="/" style="color:var(--cinza-medio);">Início</a>
    <span>›</span>
    <a href="/blog" style="color:var(--cinza-medio);">Blog</a>
    <span>›</span>
    <span style="color:var(--cinza-escuro);"><?= htmlspecialchars($post['titulo']) ?></span>
  </nav>
</div>

<!-- POST HERO -->
<section class="secao" style="padding-bottom:0;">
  <div class="container post-layout">
    <article class="post-article">
      <header class="post-header">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
          <span class="tag" style="margin:0;"><?= htmlspecialchars($categorias_label[$post['categoria']] ?? ucfirst($post['categoria'])) ?></span>
          <span style="font-size:.8rem;color:var(--cinza-medio);">
            <?= date('d \d\e F \d\e Y', strtotime($post['publicado_em'])) ?>
          </span>
        </div>
        <h1 class="post-titulo"><?= htmlspecialchars($post['titulo']) ?></h1>
        <?php if ($post['resumo']): ?>
          <p class="post-resumo"><?= htmlspecialchars($post['resumo']) ?></p>
        <?php endif; ?>
        <div class="post-autor">
          <div class="post-autor__avatar">TM</div>
          <div>
            <strong>Tamires Müller</strong>
            <span>Médica Veterinária — CRMV/RS</span>
          </div>
        </div>
      </header>

      <?php if ($post['imagem']): ?>
      <div class="post-imagem-destaque">
        <img src="<?= htmlspecialchars($post['imagem']) ?>"
             alt="<?= htmlspecialchars($post['titulo']) ?>">
      </div>
      <?php endif; ?>

      <div class="post-conteudo">
        <?= $post['conteudo'] ?>
      </div>

      <!-- CTA DENTRO DO POST -->
      <div class="post-cta">
        <div>
          <strong>Precisa de atendimento na sua propriedade?</strong>
          <p>Tamires atende na região de São Martinho, RS. Agende uma visita técnica ou conheça a parceria SENAR.</p>
        </div>
        <a href="/contato?intencao=clinica" class="btn btn--primario" style="white-space:nowrap;">
          Solicitar visita
        </a>
      </div>

      <!-- COMPARTILHAR -->
      <div class="post-compartilhar">
        <span>Compartilhar:</span>
        <a href="https://wa.me/?text=<?= urlencode($post['titulo'] . ' — ' . 'http://localhost:8000/post/' . $post['slug']) ?>"
           target="_blank" class="share-btn share-btn--wa">WhatsApp</a>
        <button onclick="copiarLink()" class="share-btn share-btn--copy" id="btn-copiar">
          Copiar link
        </button>
      </div>
    </article>

    <!-- SIDEBAR -->
    <aside class="post-sidebar">
      <div class="sidebar-card">
        <h3>Sobre a autora</h3>
        <div class="sidebar-autora">
          <div class="post-autor__avatar" style="width:52px;height:52px;font-size:.95rem;">TM</div>
          <div>
            <strong>Tamires Müller</strong>
            <p>Médica veterinária especializada em bovinos leiteiros e gestão de propriedades rurais.</p>
          </div>
        </div>
        <a href="/sobre" class="btn btn--outline" style="width:100%;justify-content:center;margin-top:1rem;font-size:.85rem;">
          Conheça minha trajetória
        </a>
      </div>

      <div class="sidebar-card">
        <h3>Agende uma visita</h3>
        <p style="font-size:.85rem;color:var(--cinza-medio);margin-bottom:1rem;">
          Atendimento em São Martinho e região. Parceria gratuita via SENAR.
        </p>
        <a href="/contato?intencao=senar" class="btn btn--primario"
           style="width:100%;justify-content:center;font-size:.85rem;">
          Quero participar do SENAR
        </a>
        <a href="/contato?intencao=clinica" class="btn btn--outline"
           style="width:100%;justify-content:center;font-size:.85rem;margin-top:.6rem;">
          Visita clínica
        </a>
      </div>

      <?php if ($relacionados): ?>
      <div class="sidebar-card">
        <h3>Leia também</h3>
        <div style="display:flex;flex-direction:column;gap:.875rem;margin-top:.5rem;">
          <?php foreach ($relacionados as $r): ?>
          <a href="/post/<?= htmlspecialchars($r['slug']) ?>" class="relacionado-item">
            <span class="relacionado-cat"><?= htmlspecialchars($categorias_label[$r['categoria']] ?? ucfirst($r['categoria'])) ?></span>
            <span class="relacionado-titulo"><?= htmlspecialchars($r['titulo']) ?></span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </aside>
  </div>
</section>

<div class="container" style="padding-bottom:var(--secao);">
  <a href="/blog" style="font-size:.88rem;color:var(--verde-medio);font-weight:600;">← Voltar ao blog</a>
</div>

<style>
.post-layout { display:grid; grid-template-columns:1fr 300px; gap:3rem; align-items:start; }
.post-header { margin-bottom:2rem; }
.post-titulo { font-size:clamp(1.6rem,3.5vw,2.4rem); color:var(--verde-escuro); line-height:1.2; margin-bottom:.875rem; }
.post-resumo { font-size:1.05rem; color:var(--cinza-medio); line-height:1.7; margin-bottom:1.25rem; }
.post-autor  { display:flex; align-items:center; gap:.875rem; padding:1rem 0;
               border-top:1px solid rgba(0,0,0,.07); border-bottom:1px solid rgba(0,0,0,.07); }
.post-autor__avatar {
  width:42px;height:42px;border-radius:50%;background:var(--verde-escuro);
  color:#fff;display:flex;align-items:center;justify-content:center;
  font-size:.75rem;font-weight:700;flex-shrink:0;
}
.post-autor strong { display:block;font-size:.88rem;color:var(--preto); }
.post-autor span   { font-size:.75rem;color:var(--cinza-medio); }
.post-imagem-destaque { margin:2rem 0; border-radius:var(--raio-md); overflow:hidden; }
.post-imagem-destaque img { width:100%;max-height:460px;object-fit:cover; }

.post-conteudo { font-size:1rem; line-height:1.85; color:var(--cinza-escuro); }
.post-conteudo h2 { font-size:1.4rem;color:var(--verde-escuro);margin:2rem 0 .75rem; }
.post-conteudo h3 { font-size:1.15rem;color:var(--verde-escuro);margin:1.5rem 0 .5rem; }
.post-conteudo p  { margin-bottom:1.25rem; }
.post-conteudo ul, .post-conteudo ol { margin:1rem 0 1.25rem 1.5rem; }
.post-conteudo li { margin-bottom:.4rem; }
.post-conteudo strong { color:var(--preto); }
.post-conteudo blockquote {
  border-left:4px solid var(--verde-claro);padding:.75rem 1.25rem;
  background:var(--verde-suave);border-radius:0 var(--raio-sm) var(--raio-sm) 0;
  margin:1.5rem 0;font-style:italic;
}

.post-cta {
  display:flex;align-items:center;gap:1.5rem;justify-content:space-between;
  background:var(--verde-suave);border-radius:var(--raio-md);
  padding:1.5rem;margin:2.5rem 0;flex-wrap:wrap;
}
.post-cta strong { display:block;color:var(--verde-escuro);margin-bottom:.3rem; }
.post-cta p { font-size:.88rem;color:var(--cinza-escuro); }

.post-compartilhar {
  display:flex;align-items:center;gap:.75rem;padding-top:1.5rem;
  border-top:1px solid rgba(0,0,0,.07);flex-wrap:wrap;
}
.post-compartilhar span { font-size:.82rem;color:var(--cinza-medio);font-weight:600; }
.share-btn {
  font-size:.78rem;font-weight:600;padding:.35rem .875rem;
  border-radius:var(--raio-full);cursor:pointer;transition:all .2s;
}
.share-btn--wa   { background:#25D366;color:#fff;border:none; }
.share-btn--copy { background:var(--cinza-claro);color:var(--cinza-escuro);
                   border:1px solid rgba(0,0,0,.1); }
.share-btn--copy:hover { background:var(--verde-suave);color:var(--verde-escuro); }

.post-sidebar { position:sticky;top:80px;display:flex;flex-direction:column;gap:1.25rem; }
.sidebar-card {
  background:#fff;border:1px solid rgba(0,0,0,.08);
  border-radius:var(--raio-md);padding:1.25rem;
}
.sidebar-card h3 { font-size:.95rem;color:var(--verde-escuro);margin-bottom:.875rem; }
.sidebar-autora  { display:flex;gap:.75rem;align-items:flex-start; }
.sidebar-autora strong { display:block;font-size:.88rem; }
.sidebar-autora p { font-size:.78rem;color:var(--cinza-medio);line-height:1.5;margin-top:.2rem; }

.relacionado-item { display:flex;flex-direction:column;gap:.2rem;padding:.6rem 0;
                    border-bottom:1px solid rgba(0,0,0,.06); }
.relacionado-item:last-child { border-bottom:none; }
.relacionado-cat   { font-size:.68rem;font-weight:700;text-transform:uppercase;
                     letter-spacing:.06em;color:var(--verde-medio); }
.relacionado-titulo{ font-size:.85rem;color:var(--preto);line-height:1.35; }
.relacionado-titulo:hover { color:var(--verde-escuro); }

@media(max-width:900px) {
  .post-layout { grid-template-columns:1fr; }
  .post-sidebar { position:static; }
}
</style>

<script>
function copiarLink() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const btn = document.getElementById('btn-copiar');
    btn.textContent = 'Link copiado!';
    btn.style.background = 'var(--verde-suave)';
    btn.style.color = 'var(--verde-medio)';
    setTimeout(() => {
      btn.textContent = 'Copiar link';
      btn.style.background = '';
      btn.style.color = '';
    }, 2500);
  });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>