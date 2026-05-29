<?php
$page_title = 'Tamires  — Médica Veterinária em São Martinho, RS';
$page_desc  = 'Atendimento veterinário especializado em bovinos leiteiros e consultoria de propriedades rurais na região de São Martinho, RS — em parceria com o SENAR.';
require_once 'config/db.php';
require_once 'config/functions.php';
$servicos = getServicos($pdo);
$posts    = getPosts($pdo, 3);
require_once 'includes/header.php';

$icones = [
  'stethoscope' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
  'chart'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
  'syringe'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
  'scissors'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/>',
  'handshake'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
];
?>

<!-- HERO -->
<section class="secao">
  <div class="container hero">
    <div class="hero__texto">
      <div class="hero__badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
        Atendimento regional — São Martinho, RS
      </div>
      <h1 class="hero__titulo">
        Saúde animal e<br><em>gestão rural</em><br>que transformam
      </h1>
      <p class="hero__desc">
        Médica veterinária especializada em bovinos leiteiros. Clínica, sanidade de rebanho e consultoria de gerenciamento em parceria com o SENAR — direto na sua propriedade.
      </p>
      <div class="hero__acoes">
        <a href="/contato?intencao=clinica" class="btn btn--primario">Solicitar visita técnica</a>
        <a href="/#servicos"   class="btn btn--outline">Ver serviços</a>
      </div>
      <div class="hero__stats">
        <div class="hero__stat"><strong>SENAR</strong><span>Parceria oficial</span></div>
        <div class="hero__stat"><strong>20km</strong><span>Raio de atendimento</span></div>
        <div class="hero__stat"><strong>100%</strong><span>Atendimento local</span></div>
      </div>
    </div>
    <div class="hero__imagem">
      <img
        src="/assets/img/posts/capa_tamires.webp"
        alt="Tamires Müller — Médica Veterinária em atendimento a bovinos leiteiros em São Martinho, RS"
        width="1598" height="720"
        loading="eager"
        fetchpriority="high"
        decoding="async"
        class="hero__img"
      >
    </div>
  </div>
</section>

<!-- SERVIÇOS -->
<section class="secao secao--alt" id="servicos">
  <div class="container">
    <div class="reveal">
      <span class="tag">O que fazemos</span>
      <h2 class="titulo-secao">Serviços especializados<br>para sua propriedade</h2>
      <p class="subtitulo-secao">Atendimento técnico focado em resultados reais — do diagnóstico clínico ao planejamento estratégico do rebanho.</p>
    </div>
    <div class="servicos__grid">
      <?php foreach ($servicos as $s): ?>
      <div class="servico-card reveal">
        <div class="servico-card__icone">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <?= $icones[$s['icone']] ?? $icones['stethoscope'] ?>
          </svg>
        </div>
        <h3><?= htmlspecialchars($s['nome']) ?></h3>
        <p><?= htmlspecialchars($s['descricao']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- SOBRE STRIP -->
<section class="sobre-strip">
  <div class="container sobre-strip__inner">
    <div class="reveal">
      <span class="tag sobre-strip__tag">Quem sou</span>
      <h2>Veterinária comprometida com o produtor rural</h2>
      <p>Formada com foco em bovinocultura leiteira, atuo na região de São Martinho desenvolvendo o potencial produtivo de cada propriedade. Não trago só a maleta — trago planejamento e resultado.</p>
      <ul class="sobre-strip__lista">
        <li>Diagnóstico completo da propriedade na primeira visita</li>
        <li>Plano de manejo personalizado para seu rebanho</li>
        <li>Acompanhamento mensal gratuito via SENAR</li>
        <li>Comunicação direta pelo WhatsApp</li>
      </ul>
      <a href="/sobre" class="btn btn--branco" style="margin-top:1.75rem;">Conheça minha trajetória</a>
    </div>
    <div class="sobre-strip__foto reveal">
      <img
        src="/assets/img/posts/quem_sou.webp"
        alt="Tamires Müller — veterinária especializada em bovinos leiteiros"
        width="798" height="1067"
        loading="lazy"
        decoding="async"
        class="sobre-strip__img"
      >
    </div>
  </div>
</section>

<!-- SENAR -->
<section class="secao senar">
  <div class="container senar__inner reveal">
    <div>
      <span class="tag">Programa gratuito</span>
      <h2>Parceria SENAR — sem custo para o produtor</h2>
      <p>Por meio do SENAR, ofereço visitas mensais gratuitas à sua propriedade com diagnóstico, análise de custos e plano de melhoria. Você não paga nada — só colhe os resultados.</p>
    </div>
    <a href="/contato?intencao=senar" class="btn btn--primario" style="white-space:nowrap;">Quero participar</a>
  </div>
</section>

<?php if ($posts): ?>
<!-- BLOG -->
<section class="secao">
  <div class="container">
    <div class="reveal" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;">
      <div>
        <span class="tag">Dicas e manejo</span>
        <h2 class="titulo-secao">Manejo, saúde e gestão no campo</h2>
      </div>
      <a href="/blog" class="btn btn--outline">Ver todos os posts</a>
    </div>
    <div class="blog__grid">
      <?php foreach ($posts as $post): ?>
      <article class="post-card reveal">
        <div class="post-card__img">
          <?php if ($post['imagem']): ?>
            <img src="<?= htmlspecialchars($post['imagem']) ?>" alt="<?= htmlspecialchars($post['titulo']) ?>" loading="lazy">
          <?php else: ?>
            <div style="width:100%;height:100%;background:var(--verde-suave);display:flex;align-items:center;justify-content:center;">
              <span style="font-size:.75rem;color:var(--verde-medio);">Imagem em breve</span>
            </div>
          <?php endif; ?>
        </div>
        <div class="post-card__corpo">
          <span class="post-card__cat"><?= htmlspecialchars(ucfirst($post['categoria'])) ?></span>
          <h3><?= htmlspecialchars($post['titulo']) ?></h3>
          <p><?= htmlspecialchars($post['resumo']) ?></p>
          <a href="/post/<?= htmlspecialchars($post['slug']) ?>" class="post-card__link">
            Ler artigo →
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- NEWSLETTER -->
<section class="secao secao--alt">
  <div class="container" style="max-width:560px;text-align:center;">
    <div class="reveal">
      <span class="tag">Fique por dentro</span>
      <h2 class="titulo-secao">Receba dicas de manejo no seu e-mail</h2>
      <p class="subtitulo-secao" style="margin-inline:auto;margin-bottom:1.5rem;">
        Conteúdo prático sobre saúde animal e gestão rural — sem spam.
      </p>
      <form id="form-newsletter" style="display:flex;gap:.75rem;flex-wrap:wrap;justify-content:center;">
        <input type="email" name="email" placeholder="seu@email.com" required
               style="flex:1;min-width:220px;padding:.7rem 1rem;border:1.5px solid rgba(0,0,0,.12);
                      border-radius:var(--raio-full);font-size:.9rem;">
        <button type="submit" class="btn btn--primario">Cadastrar</button>
      </form>
      <div id="news-aviso" class="form__aviso" style="margin-top:.75rem;text-align:left;"></div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<?php /* CSS das imagens adicionadas — mover para o stylesheet principal se preferir */ ?>
<style>
/* ── Hero image ── */
.hero__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: var(--raio-lg, 1rem);
  display: block;
}

/* ── Sobre-strip image ── */
.sobre-strip__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: top center;
  border-radius: var(--raio-lg, 1rem);
  display: block;
}
</style>