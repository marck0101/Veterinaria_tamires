<?php
require_once __DIR__ . '/admin-auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../config/mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$posts = $pdo->query(
    "SELECT * FROM posts WHERE publicado = 1 ORDER BY publicado_em DESC"
)->fetchAll();

$assinantes = $pdo->query(
    "SELECT * FROM newsletter WHERE ativo = 1"
)->fetchAll();

$resultado  = null;
$log_envios = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = (int)($_POST['post_id'] ?? 0);
    $assunto = sanitize($_POST['assunto'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND publicado = 1");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post || !$assunto) {
        $resultado = ['tipo' => 'erro', 'msg' => 'Selecione um post e informe o assunto.'];
    } else {
        $url_post = SITE_URL . '/post/' . $post['slug'];
        $enviados = 0;
        $erros    = 0;

        foreach ($assinantes as $assinante) {
            $corpo = "
            <p>Olá,</p>
            <p>Publicamos um novo conteúdo que pode ser útil para você:</p>
            <table width='100%' style='background:#F0F7F4;border-radius:8px;
                   padding:20px 24px;border-left:4px solid #1B4332;'>
              <tr><td>
                <p style='margin:0 0 6px;font-size:11px;color:#52B788;font-weight:700;
                           text-transform:uppercase;letter-spacing:.07em;'>
                  " . htmlspecialchars(ucfirst($post['categoria'])) . "
                </p>
                <h2 style='margin:0 0 8px;font-size:18px;color:#1B4332;'>
                  " . htmlspecialchars($post['titulo']) . "
                </h2>
                <p style='margin:0;font-size:14px;color:#555;line-height:1.6;'>
                  " . htmlspecialchars($post['resumo']) . "
                </p>
              </td></tr>
            </table>
            <p style='margin-top:24px;'>
              <a href='{$url_post}'
                 style='background:#1B4332;color:#fff;text-decoration:none;
                        padding:12px 28px;border-radius:999px;
                        font-size:14px;font-weight:600;'>
                Ler artigo completo →
              </a>
            </p>
            <p style='font-size:13px;color:#888;margin-top:20px;'>
              Se o botão não funcionar, copie e cole este link:<br>
              <a href='{$url_post}' style='color:#1B4332;'>{$url_post}</a>
            </p>";

            $html = templateEmail(
                htmlspecialchars($post['titulo']),
                $corpo,
                $assinante['email'],
                $assinante['token']
            );

            try {
                $mail = criarMailer();
                $mail->addAddress($assinante['email']);
                $mail->Subject = $assunto;
                $mail->isHTML(true);
                $mail->Body    = $html;
                $mail->AltBody = strip_tags($post['resumo'])
                    . "\n\nLer: {$url_post}"
                    . "\n\nDescadastrar: " . SITE_URL
                    . '/descadastrar?token=' . $assinante['token'];
                $mail->send();
                $enviados++;
                $log_envios[] = ['email' => $assinante['email'], 'status' => 'ok'];
            } catch (Exception $e) {
                $erros++;
                $log_envios[] = [
                    'email'   => $assinante['email'],
                    'status'  => 'erro',
                    'detalhe' => $e->getMessage(),
                ];
            }
        }

        $resultado = [
            'tipo' => $erros === 0 ? 'sucesso' : ($enviados > 0 ? 'parcial' : 'erro'),
            'msg'  => "Enviado para {$enviados} assinante(s)."
                    . ($erros > 0 ? " {$erros} erro(s)." : ''),
        ];
    }
}

$cats = [
    'manejo' => 'Manejo',
    'gestao' => 'Gestão',
    'saude'  => 'Saúde animal',
    'senar'  => 'SENAR',
];

$meses_pt = [
    '01'=>'Janeiro','02'=>'Fevereiro','03'=>'Março','04'=>'Abril',
    '05'=>'Maio','06'=>'Junho','07'=>'Julho','08'=>'Agosto',
    '09'=>'Setembro','10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro',
];
$meses_abrev = [
    '01'=>'Jan','02'=>'Fev','03'=>'Mar','04'=>'Abr',
    '05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Ago',
    '09'=>'Set','10'=>'Out','11'=>'Nov','12'=>'Dez',
];

$meses_disponiveis = [];
foreach ($posts as $p) {
    $m     = date('m', strtotime($p['publicado_em']));
    $a     = date('Y', strtotime($p['publicado_em']));
    $chave = $a . '-' . $m;
    if (!isset($meses_disponiveis[$chave])) {
        $meses_disponiveis[$chave] = ($meses_pt[$m] ?? $m) . ' de ' . $a;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enviar Newsletter — Admin</title>
  <link rel="stylesheet" href="/admin/admin.css">
  <style>
    .filtros-painel {
      background:var(--abg); border:1px solid var(--aborda);
      border-radius:10px; padding:1rem 1.25rem; margin-bottom:.75rem;
    }
    .filtros-painel__titulo {
      font-size:.72rem; font-weight:700; color:var(--atxt2);
      text-transform:uppercase; letter-spacing:.06em;
      display:flex; align-items:center; gap:.4rem; margin-bottom:.875rem;
    }
    .filtros-grade {
      display:grid; grid-template-columns:1.4fr 1fr 1fr; gap:.75rem;
    }
    .filtro-bloco label {
      font-size:.72rem; font-weight:600; color:var(--atxt2);
      display:block; margin-bottom:.3rem;
    }
    .filtro-bloco input,
    .filtro-bloco select {
      width:100%; padding:.5rem .75rem;
      border:1.5px solid var(--aborda); border-radius:8px;
      font-size:.82rem; background:#fff; transition:border-color .2s;
    }
    .filtro-bloco input:focus,
    .filtro-bloco select:focus { outline:none; border-color:#1B4332; }
    .busca-wrap { position:relative; }
    .busca-wrap svg {
      position:absolute; left:.65rem; top:50%;
      transform:translateY(-50%); width:13px;
      color:var(--atxt2); pointer-events:none;
    }
    .busca-wrap input { padding-left:2rem; }
    #btn-clr-busca {
      position:absolute; right:.65rem; top:50%;
      transform:translateY(-50%); background:none; border:none;
      cursor:pointer; color:var(--atxt2); font-size:.95rem;
      line-height:1; display:none;
    }
    #btn-clr-busca:hover { color:#c00; }
    .filtros-rodape {
      display:flex; align-items:center; justify-content:space-between;
      margin-top:.75rem; flex-wrap:wrap; gap:.4rem;
    }
    #cont-resultados { font-weight:700; color:#1B4332; }

    /* lista */
    #lista-posts {
      border:1.5px solid var(--aborda); border-radius:10px;
      max-height:320px; overflow-y:auto; margin-top:.25rem;
    }
    .post-item {
      display:grid; grid-template-columns:52px 1fr 20px;
      gap:.875rem; align-items:center;
      padding:.875rem 1rem; cursor:pointer;
      border-bottom:1px solid var(--aborda); transition:background .12s;
    }
    .post-item:last-of-type { border-bottom:none; }
    .post-item:hover { background:#F0FDF4; }
    .post-item.selecionado { background:#ECFDF5; }
    .post-item.oculto { display:none; }
    .pi-data { text-align:center; }
    .pi-dia  { font-size:1.15rem; font-weight:700; color:#1B4332; line-height:1; }
    .pi-mes  { font-size:.62rem; font-weight:700; text-transform:uppercase;
               letter-spacing:.06em; color:#52B788; }
    .pi-ano  { font-size:.6rem; color:var(--atxt2); }
    .pi-titulo { font-size:.88rem; font-weight:600; color:var(--atxt);
                 margin:0 0 .2rem; }
    .pi-meta   { font-size:.73rem; color:var(--atxt2); margin:0;
                 display:flex; gap:.4rem; align-items:center; flex-wrap:wrap; }
    .pi-cat    { font-size:.63rem; font-weight:700; text-transform:uppercase;
                 letter-spacing:.05em; padding:.1rem .5rem;
                 border-radius:20px; background:#D1FAE5; color:#065F46; }
    .pi-check  {
      width:18px; height:18px; border-radius:50%;
      background:#1B4332; display:none;
      align-items:center; justify-content:center; flex-shrink:0;
    }
    .pi-check svg { width:10px; }
    .post-item.selecionado .pi-check { display:flex; }
    .lista-vazia {
      padding:2.5rem; text-align:center;
      font-size:.85rem; color:var(--atxt2); display:none;
    }
    .lista-vazia svg { width:30px; opacity:.3; margin:0 auto .5rem; display:block; }

    /* pill */
    .post-pill {
      display:none; margin-top:.75rem; padding:.75rem 1rem;
      background:#ECFDF5; border:1.5px solid #6EE7B7; border-radius:10px;
    }
    .post-pill__inner {
      display:flex; align-items:center; gap:.75rem;
    }
    .post-pill__ico {
      width:30px; height:30px; border-radius:50%; background:#1B4332;
      display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .post-pill__ico svg { width:13px; }
    .post-pill__info { flex:1; }
    .post-pill__titulo { font-size:.85rem; font-weight:700; color:#065F46; margin:0; }
    .post-pill__meta   { font-size:.72rem; color:#52B788; margin:.1rem 0 0; }
    .post-pill__rmv {
      background:none; border:none; cursor:pointer;
      color:#52B788; font-size:1.1rem; line-height:1;
      padding:.2rem; border-radius:50%; transition:all .15s;
    }
    .post-pill__rmv:hover { background:#D1FAE5; color:#065F46; }

    .preview-box {
      display:none; margin:1rem 0; padding:1rem 1.25rem;
      background:#F0F7F4; border-radius:8px; border-left:4px solid #1B4332;
    }

    @media(max-width:640px) {
      .filtros-grade { grid-template-columns:1fr; }
    }
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">

    <div class="topbar">
      <h1>Enviar newsletter</h1>
      <a href="/admin/newsletter.php" class="btn-admin btn--outline btn--sm">
        ← Lista de e-mails
      </a>
    </div>

    <div class="content">

      <?php if ($resultado): ?>
        <div class="alert alert--<?= $resultado['tipo'] === 'sucesso' ? 'sucesso' : 'erro' ?>"
             id="alerta">
          <?= htmlspecialchars($resultado['msg']) ?>
        </div>
      <?php endif; ?>

      <!-- Métricas -->
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
        <div class="metrica-card">
          <div class="metrica-card__label">Assinantes ativos</div>
          <div class="metrica-card__valor"><?= count($assinantes) ?></div>
        </div>
        <div class="metrica-card">
          <div class="metrica-card__label">Posts publicados</div>
          <div class="metrica-card__valor"><?= count($posts) ?></div>
        </div>
        <div class="metrica-card">
          <div class="metrica-card__label">Destinatários</div>
          <div class="metrica-card__valor"><?= count($assinantes) ?></div>
          <div class="metrica-card__sub">neste envio</div>
        </div>
      </div>

      <div class="card" style="padding:1.75rem;">
        <h2 style="font-size:1rem;margin-bottom:1.5rem;">
          Selecionar post e enviar
        </h2>

        <form method="POST" id="form-nl">
          <input type="hidden" name="post_id" id="campo-post-id">

          <!-- FILTROS -->
          <div class="filtros-painel">
            <div class="filtros-painel__titulo">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2" style="width:13px;">
                <path d="M3 4h18M7 8h10M11 12h2M11 16h2"/>
              </svg>
              Filtros
            </div>

            <div class="filtros-grade">
              <!-- Busca por título -->
              <div class="filtro-bloco">
                <label>Buscar por título</label>
                <div class="busca-wrap">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                  </svg>
                  <input type="text" id="busca-titulo"
                         placeholder="Digite o título..."
                         oninput="aplicarFiltros()">
                  <button type="button" id="btn-clr-busca"
                          onclick="limparBusca()">✕</button>
                </div>
              </div>

              <!-- Categoria -->
              <div class="filtro-bloco">
                <label>Categoria</label>
                <select id="filtro-cat" onchange="aplicarFiltros()">
                  <option value="">Todas</option>
                  <?php foreach ($cats as $v => $l): ?>
                    <option value="<?= $v ?>"><?= $l ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Mês -->
              <div class="filtro-bloco">
                <label>Mês de publicação</label>
                <select id="filtro-mes" onchange="aplicarFiltros()">
                  <option value="">Todos os meses</option>
                  <?php foreach ($meses_disponiveis as $chave => $label): ?>
                    <option value="<?= $chave ?>"><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="filtros-rodape">
              <span style="font-size:.75rem;color:var(--atxt2);">
                Mostrando <span id="cont-resultados"><?= count($posts) ?></span>
                de <?= count($posts) ?> posts
              </span>
              <button type="button" onclick="limparFiltros()"
                      id="btn-clr-filtros"
                      class="btn-admin btn--outline btn--sm"
                      style="display:none;font-size:.73rem;">
                ✕ Limpar filtros
              </button>
            </div>
          </div>

          <!-- LISTA -->
          <div id="lista-posts">
            <?php foreach ($posts as $p):
              $m   = date('m', strtotime($p['publicado_em']));
              $a   = date('Y', strtotime($p['publicado_em']));
              $dia = date('d', strtotime($p['publicado_em']));
              $mes_chave = $a . '-' . $m;
              $cat_label = $cats[$p['categoria']] ?? ucfirst($p['categoria']);
              $resumo_curto = mb_strlen($p['resumo']) > 65
                ? mb_substr($p['resumo'], 0, 65) . '…'
                : $p['resumo'];
            ?>
            <div class="post-item"
                 data-id="<?= $p['id'] ?>"
                 data-titulo="<?= strtolower(htmlspecialchars($p['titulo'])) ?>"
                 data-titulo-original="<?= htmlspecialchars($p['titulo']) ?>"
                 data-resumo="<?= htmlspecialchars($p['resumo']) ?>"
                 data-cat="<?= htmlspecialchars($p['categoria']) ?>"
                 data-cat-label="<?= htmlspecialchars($cat_label) ?>"
                 data-mes="<?= $mes_chave ?>"
                 data-data="<?= date('d/m/Y', strtotime($p['publicado_em'])) ?>"
                 onclick="selecionarPost(this)">

              <div class="pi-data">
                <div class="pi-dia"><?= $dia ?></div>
                <div class="pi-mes"><?= $meses_abrev[$m] ?? $m ?></div>
                <div class="pi-ano"><?= $a ?></div>
              </div>

              <div>
                <p class="pi-titulo"><?= htmlspecialchars($p['titulo']) ?></p>
                <p class="pi-meta">
                  <span class="pi-cat"><?= htmlspecialchars($cat_label) ?></span>
                  <?= htmlspecialchars($resumo_curto) ?>
                </p>
              </div>

              <div class="pi-check">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3">
                  <path d="M20 6L9 17l-5-5"/>
                </svg>
              </div>
            </div>
            <?php endforeach; ?>

            <div class="lista-vazia" id="lista-vazia">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Nenhum post encontrado com esses filtros.
            </div>
          </div>

          <!-- Pill do post selecionado -->
          <div class="post-pill" id="post-pill">
            <div class="post-pill__inner">
              <div class="post-pill__ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                  <path d="M20 6L9 17l-5-5"/>
                </svg>
              </div>
              <div class="post-pill__info">
                <p class="post-pill__titulo" id="pill-titulo"></p>
                <p class="post-pill__meta"   id="pill-meta"></p>
              </div>
              <button type="button" class="post-pill__rmv"
                      onclick="limparSelecao()" title="Remover">✕</button>
            </div>
          </div>

          <!-- Assunto -->
          <div class="campo" style="margin-top:1.25rem;">
            <label>Assunto do e-mail *</label>
            <input type="text" name="assunto" id="campo-assunto" required
                   placeholder="Ex: Novo artigo: Como planejar o desmame"
                   value="<?= htmlspecialchars($_POST['assunto'] ?? '') ?>"
                   oninput="contarChars()">
            <span style="font-size:.72rem;color:var(--atxt2);margin-top:.3rem;display:block;">
              Recomendado: 30–60 caracteres.
              <span id="cont-chars" style="font-weight:600;"></span>
            </span>
          </div>

          <!-- Preview -->
          <div class="preview-box" id="preview-post">
            <p id="prev-cat"    style="margin:0 0 4px;font-size:.7rem;font-weight:700;
               color:#52B788;text-transform:uppercase;letter-spacing:.07em;"></p>
            <p id="prev-titulo" style="margin:0 0 6px;font-size:.95rem;
               font-weight:700;color:#1B4332;"></p>
            <p id="prev-resumo" style="margin:0;font-size:.85rem;color:#555;"></p>
          </div>

          <!-- Boas práticas -->
          <div style="background:#FEF3C7;border:1px solid #FCD34D;border-radius:8px;
                      padding:1rem 1.25rem;margin:1rem 0;font-size:.82rem;color:#78350F;">
            <strong>Boas práticas:</strong> envie no máximo 1–2 newsletters por semana.
            Horários com maior abertura: <strong>terça a quinta, entre 9h e 11h</strong>.
            Evite "grátis", "clique aqui" ou excesso de maiúsculas no assunto.
          </div>

          <div style="display:flex;gap:.75rem;align-items:center;margin-top:1.25rem;">
            <button type="submit" class="btn-admin btn--verde"
                    onclick="return confirmarEnvio()">
              Enviar para <?= count($assinantes) ?>
              assinante<?= count($assinantes) !== 1 ? 's' : '' ?>
            </button>
            <span style="font-size:.78rem;color:var(--atxt2);">
              Um e-mail será enviado para cada assinante ativo.
            </span>
          </div>
        </form>
      </div>

      <!-- Log -->
      <?php if ($log_envios): ?>
      <div class="card" style="margin-top:1.25rem;">
        <div class="card__header"><h2>Log do envio</h2></div>
        <table class="table">
          <thead>
            <tr><th>E-mail</th><th>Status</th><th>Detalhe</th></tr>
          </thead>
          <tbody>
            <?php foreach ($log_envios as $l): ?>
            <tr>
              <td><?= htmlspecialchars($l['email']) ?></td>
              <td>
                <span class="badge <?= $l['status']==='ok' ? 'badge--verde':'badge--cinza' ?>">
                  <?= $l['status'] === 'ok' ? 'Enviado' : 'Erro' ?>
                </span>
              </td>
              <td style="font-size:.78rem;color:var(--atxt2);">
                <?= htmlspecialchars($l['detalhe'] ?? '—') ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

    </div>
  </main>
</div>

<script>
const totalPosts = <?= count($posts) ?>;

// ── Filtros ─────────────────────────────────────────────────────
function aplicarFiltros() {
  const termo = document.getElementById('busca-titulo').value.toLowerCase().trim();
  const cat   = document.getElementById('filtro-cat').value;
  const mes   = document.getElementById('filtro-mes').value;

  let visiveis = 0;
  document.querySelectorAll('.post-item').forEach(item => {
    const ok = (!termo || item.dataset.titulo.includes(termo))
            && (!cat   || item.dataset.cat === cat)
            && (!mes   || item.dataset.mes === mes);
    item.classList.toggle('oculto', !ok);
    if (ok) visiveis++;
  });

  document.getElementById('cont-resultados').textContent = visiveis;
  document.getElementById('lista-vazia').style.display =
    visiveis === 0 ? 'block' : 'none';

  const temFiltro = termo || cat || mes;
  document.getElementById('btn-clr-filtros').style.display =
    temFiltro ? 'inline-flex' : 'none';
  document.getElementById('btn-clr-busca').style.display =
    termo ? 'block' : 'none';
}

function limparBusca() {
  document.getElementById('busca-titulo').value = '';
  aplicarFiltros();
  document.getElementById('busca-titulo').focus();
}

function limparFiltros() {
  document.getElementById('busca-titulo').value = '';
  document.getElementById('filtro-cat').value   = '';
  document.getElementById('filtro-mes').value   = '';
  aplicarFiltros();
}

// ── Selecionar post ─────────────────────────────────────────────
function selecionarPost(el) {
  document.querySelectorAll('.post-item').forEach(i =>
    i.classList.remove('selecionado')
  );
  el.classList.add('selecionado');

  document.getElementById('campo-post-id').value = el.dataset.id;

  document.getElementById('pill-titulo').textContent = el.dataset.tituloOriginal;
  document.getElementById('pill-meta').textContent   =
    el.dataset.catLabel + ' · ' + el.dataset.data;
  document.getElementById('post-pill').style.display = 'block';

  document.getElementById('prev-cat').textContent    = el.dataset.catLabel;
  document.getElementById('prev-titulo').textContent = el.dataset.tituloOriginal;
  document.getElementById('prev-resumo').textContent = el.dataset.resumo;
  document.getElementById('preview-post').style.display = 'block';

  const assunto = document.getElementById('campo-assunto');
  if (!assunto.value) {
    assunto.value = 'Novo artigo: ' + el.dataset.tituloOriginal;
    contarChars();
  }

  setTimeout(() => {
    document.getElementById('campo-assunto')
      .scrollIntoView({ behavior:'smooth', block:'center' });
  }, 180);
}

function limparSelecao() {
  document.getElementById('campo-post-id').value = '';
  document.getElementById('post-pill').style.display = 'none';
  document.getElementById('preview-post').style.display = 'none';
  document.querySelectorAll('.post-item').forEach(i =>
    i.classList.remove('selecionado')
  );
}

// ── Contador de chars ───────────────────────────────────────────
function contarChars() {
  const v  = document.getElementById('campo-assunto').value.length;
  const el = document.getElementById('cont-chars');
  el.textContent = v + ' caracteres';
  el.style.color = v < 30 ? '#c00' : v > 60 ? '#d97706' : '#065F46';
}

// ── Confirmação ─────────────────────────────────────────────────
function confirmarEnvio() {
  const total   = <?= count($assinantes) ?>;
  const postId  = document.getElementById('campo-post-id').value;
  const assunto = document.getElementById('campo-assunto').value.trim();
  const titulo  = document.getElementById('pill-titulo').textContent;

  if (!postId) {
    alert('Selecione um post da lista antes de enviar.');
    return false;
  }
  if (!assunto) {
    alert('Informe o assunto do e-mail.');
    document.getElementById('campo-assunto').focus();
    return false;
  }
  return confirm(
    '📧 Confirmar envio\n\n'
    + 'Post: "' + titulo + '"\n'
    + 'Assunto: "' + assunto + '"\n'
    + 'Destinatários: ' + total + ' assinante(s)\n\n'
    + 'Esta ação enviará e-mails reais. Continuar?'
  );
}

// ── Alerta some após 4s ─────────────────────────────────────────
const alerta = document.getElementById('alerta');
if (alerta) setTimeout(() => {
  alerta.style.transition = 'opacity .5s';
  alerta.style.opacity    = '0';
}, 4000);
</script>
</body>
</html>