<?php
$page_title = 'Blog — Dicas de manejo e gestão rural';
$page_desc  = 'Artigos práticos sobre saúde animal, manejo bovino e gestão de propriedades — por Tamires Miller.';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../includes/header.php';

$todos = $pdo->query(
    "SELECT * FROM posts WHERE publicado = 1 ORDER BY publicado_em DESC"
)->fetchAll();

$categorias = [
    'manejo' => 'Manejo',
    'gestao' => 'Gestão',
    'saude'  => 'Saúde animal',
    'senar'  => 'SENAR',
];
?>

<section class="secao" style="padding-bottom:0;">
  <div class="container">
    <div class="reveal" style="max-width:600px;">
      <span class="tag">Blog</span>
      <h1 class="titulo-secao">Manejo, saúde e gestão no campo</h1>
      <p class="subtitulo-secao">
         Artigos práticos sobre animais, propriedade e produção rural — por Tamires Müller.
      </p>
    </div>

    <!-- FILTROS MÚLTIPLOS -->
    <div class="filtros-wrapper reveal" id="filtros-blog">
      <div class="filtros-bar">
        <span class="filtros-label">Filtrar por:</span>
        <?php foreach ($categorias as $val => $label): ?>
          <button class="filtro-chip"
                  data-filtro-val="<?= $val ?>"
                  onclick="filtroBlog.toggle('<?= $val ?>')"
                  aria-pressed="false">
            <?= $label ?>
          </button>
        <?php endforeach; ?>
        <button class="filtro-limpar"
                data-filtro-limpar
                onclick="filtroBlog.limpar()"
                style="display:none;">
          ✕ Limpar filtros
        </button>
      </div>
      <div id="filtro-chips-ativos" style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.6rem;min-height:24px;"></div>
    </div>
  </div>
</section>

<section class="secao">
  <div class="container">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
      <p style="font-size:.82rem;color:var(--cinza-medio);" id="filtro-contador"></p>
    </div>

    <div class="blog__grid" id="grade-posts">
      <?php foreach ($todos as $post): ?>
      <article class="post-card reveal"
               data-cat="<?= htmlspecialchars($post['categoria']) ?>">
        <a href="/post/<?= htmlspecialchars($post['slug']) ?>" class="post-card__img">
          <?php if ($post['imagem']): ?>
            <img src="<?= htmlspecialchars($post['imagem']) ?>"
                 alt="<?= htmlspecialchars($post['titulo']) ?>" loading="lazy">
          <?php else: ?>
            <div class="post-card__sem-img">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="1.5" style="width:28px;opacity:.25;">
                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          <?php endif; ?>
        </a>
        <div class="post-card__corpo">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
            <span class="post-card__cat">
              <?= htmlspecialchars($categorias[$post['categoria']] ?? ucfirst($post['categoria'])) ?>
            </span>
            <span style="font-size:.72rem;color:var(--cinza-medio);">
              <?= date('d/m/Y', strtotime($post['publicado_em'])) ?>
            </span>
          </div>
          <h2 style="font-size:1rem;">
            <a href="/post/<?= htmlspecialchars($post['slug']) ?>"
               style="color:var(--preto);">
              <?= htmlspecialchars($post['titulo']) ?>
            </a>
          </h2>
          <p><?= htmlspecialchars($post['resumo']) ?></p>
          <a href="/post/<?= htmlspecialchars($post['slug']) ?>" class="post-card__link">
            Ler artigo →
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Estado vazio -->
    <div data-filtro-vazio style="display:none;text-align:center;padding:3rem 0;">
      <p style="color:var(--cinza-medio);font-size:.95rem;">
        Nenhum post encontrado para os filtros selecionados.
      </p>
      <button onclick="filtroBlog.limpar()"
              class="btn btn--outline" style="margin-top:1rem;">
        Ver todos os posts
      </button>
    </div>

  </div>
</section>

<style>
.filtros-wrapper { margin-top:1.75rem; padding:1.25rem 1.5rem;
                   background:var(--cinza-claro); border-radius:var(--raio-md); }
.filtros-bar     { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; }
.filtros-label   { font-size:.75rem; font-weight:700; color:var(--cinza-medio);
                   text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }

.filtro-chip {
  font-size:.82rem; font-weight:600; padding:.4rem 1rem;
  border-radius:var(--raio-full); border:1.5px solid rgba(0,0,0,.12);
  color:var(--cinza-escuro); background:#fff; cursor:pointer;
  transition:all .18s; display:inline-flex; align-items:center; gap:.4rem;
}
.filtro-chip:hover { border-color:var(--verde-claro); color:var(--verde-escuro); }
.filtro-chip.ativo {
  background:var(--verde-escuro); color:#fff;
  border-color:var(--verde-escuro);
}
.filtro-chip.ativo::before { content:'✓ '; font-size:.75rem; }

.filtro-limpar {
  font-size:.78rem; font-weight:600; color:var(--cinza-medio);
  border:1.5px solid rgba(0,0,0,.1); background:#fff; border-radius:var(--raio-full);
  padding:.35rem .875rem; cursor:pointer; transition:all .15s;
  display:inline-flex; align-items:center; gap:.3rem;
}
.filtro-limpar:hover { color:#c00; border-color:#fca5a5; background:#fef2f2; }

.post-card { transition: opacity .25s, transform .25s; }
.post-card__sem-img {
  width:100%; height:100%; background:var(--verde-suave);
  display:flex; align-items:center; justify-content:center;
}
</style>

<script src="/assets/js/filtros.js"></script>
<script>
const filtroBlog = new FiltroMultiplo({
  container : '#filtros-blog',
  itens     : '#grade-posts .post-card',
  atributo  : 'data-cat',
  paramURL  : 'cat',
  contador  : '#filtro-contador',
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>