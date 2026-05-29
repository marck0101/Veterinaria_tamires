<?php
require_once __DIR__ . '/admin-auth.php';
require_once __DIR__ . '/../config/db.php';

// Ações
if (isset($_GET['lido'])) {
    $pdo->prepare("UPDATE contatos SET lido = 1 WHERE id = ?")->execute([(int)$_GET['lido']]);
    header('Location: /admin/contatos.php?msg=lido');
    exit;
}
if (isset($_GET['reabrir'])) {
    $pdo->prepare("UPDATE contatos SET lido = 0 WHERE id = ?")->execute([(int)$_GET['reabrir']]);
    header('Location: /admin/contatos.php?msg=reaberto');
    exit;
}
if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM contatos WHERE id = ?")->execute([(int)$_GET['deletar']]);
    header('Location: /admin/contatos.php?msg=deletado');
    exit;
}

$novos     = $pdo->query("SELECT * FROM contatos WHERE lido = 0 ORDER BY criado_em DESC")->fetchAll();
$atendidos = $pdo->query("SELECT * FROM contatos WHERE lido = 1 ORDER BY criado_em DESC")->fetchAll();
$total_novos = count($novos);

$labels = [
    'clinica'      => ['Clínica',       'badge--azul'],
    'consultoria'  => ['Consultoria',   'badge--verde'],
    'procedimento' => ['Procedimento',  'badge--laranja'],
    'senar'        => ['SENAR',         'badge--roxo'],
    'outro'        => ['Outro',         'badge--cinza'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contatos — Admin</title>
  <link rel="stylesheet" href="/admin/admin.css">
  <style>
    .badge--roxo { background:#EDE9FE; color:#5B21B6; }

    .contato-card {
      background:#fff; border:1px solid var(--aborda); border-radius:10px;
      padding:1.25rem 1.5rem; margin-bottom:.875rem;
      display:grid; grid-template-columns:1fr auto;
      gap:1rem; align-items:start; transition:box-shadow .2s, opacity .3s;
    }
    .contato-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.07); }
    .contato-card.novo  { border-left:4px solid #1B4332; }
    .contato-card.lido  { border-left:4px solid var(--aborda); opacity:.82; }
    .contato-card[style*="display: none"] { display:none !important; }

    .contato-card__topo {
      display:flex; align-items:center; gap:.75rem;
      flex-wrap:wrap; margin-bottom:.6rem;
    }
    .contato-nome { font-size:.95rem; font-weight:700; color:var(--atxt); }
    .contato-tel  { font-size:.85rem; color:#1B4332; font-weight:600; }
    .contato-data { font-size:.75rem; color:var(--atxt2); margin-left:auto; }
    .novo-dot     { width:8px; height:8px; border-radius:50%;
                    background:#1B4332; flex-shrink:0; }

    .contato-card__info { display:flex; gap:1.5rem; flex-wrap:wrap; margin-bottom:.6rem; }
    .info-item { display:flex; flex-direction:column; gap:.1rem; }
    .info-item label { font-size:.68rem; font-weight:700; text-transform:uppercase;
                       letter-spacing:.06em; color:var(--atxt2); }
    .info-item span  { font-size:.85rem; color:var(--atxt); }

    .contato-msg {
      font-size:.85rem; color:var(--atxt2); background:var(--abg);
      border-radius:6px; padding:.6rem .875rem; margin-top:.5rem;
      border-left:3px solid var(--aborda); line-height:1.5;
    }
    .contato-acoes { display:flex; flex-direction:column; gap:.5rem; align-items:flex-end; }

    /* Seção atendidos colapsável */
    .secao-titulo {
      display:flex; align-items:center; justify-content:space-between;
      margin:2rem 0 1rem; cursor:pointer; user-select:none;
      padding:.75rem 1rem; background:var(--abg);
      border-radius:var(--raio); border:1px solid var(--aborda);
    }
    .secao-titulo:hover { background:#eaeaea; }
    .secao-titulo h2 { font-size:.9rem; font-weight:600; color:var(--atxt2);
                       display:flex; align-items:center; gap:.6rem; margin:0; }
    .chevron { font-size:.72rem; color:var(--atxt2);
               transition:transform .25s; display:inline-block; }
    .chevron.aberto { transform:rotate(180deg); }
    .secao-conteudo { display:none; }
    .secao-conteudo.aberto { display:block; }

    /* Filtros */
    .filtros-wrapper {
      background:var(--abg); border:1px solid var(--aborda);
      border-radius:var(--raio); padding:1rem 1.25rem; margin-bottom:1.5rem;
    }
    .filtros-row { display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; }
    .filtros-label { font-size:.72rem; font-weight:700; color:var(--atxt2);
                     text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }
    .filtro-chip-admin {
      font-size:.78rem; font-weight:600; padding:.35rem .875rem;
      border-radius:20px; border:1.5px solid var(--aborda);
      color:var(--atxt2); background:#fff; cursor:pointer; transition:all .15s;
      display:inline-flex; align-items:center; gap:.3rem;
    }
    .filtro-chip-admin:hover { border-color:#1B4332; color:#1B4332; }
    .filtro-chip-admin.ativo {
      background:#1B4332; color:#fff; border-color:#1B4332;
    }
    .filtro-chip-admin.ativo::before { content:'✓ '; font-size:.7rem; }
    .filtro-limpar-admin {
      font-size:.75rem; font-weight:600; padding:.3rem .75rem;
      border-radius:20px; border:1.5px solid rgba(0,0,0,.1);
      color:var(--atxt2); background:#fff; cursor:pointer; transition:all .15s;
      display:none;
    }
    .filtro-limpar-admin:hover { color:#c00; border-color:#fca5a5; }
    .filtro-resumo {
      font-size:.75rem; color:var(--atxt2); margin-top:.6rem;
      display:none; align-items:center; gap:.5rem; flex-wrap:wrap;
    }
    .filtro-tag-ativa {
      font-size:.72rem; font-weight:600; padding:.15rem .6rem;
      background:#1B4332; color:#fff; border-radius:20px;
    }

    /* Vazio */
    .estado-vazio {
      text-align:center; padding:2.5rem 1rem; color:var(--atxt2);
      border:1.5px dashed var(--aborda); border-radius:10px;
    }
    .estado-vazio svg { width:36px; opacity:.3; margin:0 auto .75rem; display:block; }

    /* Contador badge */
    .contagem-badge {
      display:inline-flex; align-items:center; justify-content:center;
      background:#1B4332; color:#fff; border-radius:20px;
      font-size:.7rem; font-weight:700; padding:.15rem .6rem; min-width:20px;
    }

    /* Seção vazia filtrada */
    .secao-vazio-filtro {
      display:none; text-align:center; padding:1.5rem;
      color:var(--atxt2); font-size:.85rem;
      border:1.5px dashed var(--aborda); border-radius:10px; margin-bottom:1rem;
    }
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">

    <div class="topbar">
      <div style="display:flex;align-items:center;gap:.75rem;">
        <h1>Agendamentos e contatos</h1>
        <?php if ($total_novos > 0): ?>
          <span class="contagem-badge">
            <?= $total_novos ?> novo<?= $total_novos > 1 ? 's' : '' ?>
          </span>
        <?php endif; ?>
      </div>
      <a href="/contato" target="_blank" class="btn-admin btn--outline btn--sm">
        Ver formulário ↗
      </a>
    </div>

    <div class="content">

      <?php if (isset($_GET['msg'])): ?>
        <?php $alertas = [
          'lido'     => ['sucesso', '✓ Marcado como atendido.'],
          'reaberto' => ['sucesso', '✓ Movido de volta para pendentes.'],
          'deletado' => ['erro',    '✗ Contato removido.'],
        ]; [$tipo, $texto] = $alertas[$_GET['msg']] ?? ['sucesso', '']; ?>
        <div class="alert alert--<?= $tipo ?>" id="alerta"><?= $texto ?></div>
      <?php endif; ?>

      <!-- ── FILTROS MÚLTIPLOS ── -->
      <div class="filtros-wrapper" id="filtros-admin">
        <div class="filtros-row">
          <span class="filtros-label">Filtrar por:</span>
          <?php foreach ([
            'clinica'      => 'Clínica',
            'consultoria'  => 'Consultoria',
            'procedimento' => 'Procedimento',
            'senar'        => 'SENAR',
            'outro'        => 'Outro',
          ] as $val => $label): ?>
            <button class="filtro-chip-admin"
                    data-filtro-val="<?= $val ?>"
                    onclick="filtroAdmin.toggle('<?= $val ?>')"
                    aria-pressed="false">
              <?= $label ?>
            </button>
          <?php endforeach; ?>
          <button class="filtro-limpar-admin"
                  id="btn-limpar"
                  onclick="filtroAdmin.limpar()">
            ✕ Limpar filtros
          </button>
          <span id="filtro-admin-contador"
                style="margin-left:auto;font-size:.75rem;color:var(--atxt2);display:none;"></span>
        </div>
        <div class="filtro-resumo" id="filtro-resumo">
          <span style="font-size:.72rem;color:var(--atxt2);">Mostrando:</span>
        </div>
      </div>

      <!-- ── PENDENTES ── -->
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
        <h2 style="font-size:.95rem;font-weight:700;color:var(--atxt);
                   display:flex;align-items:center;gap:.5rem;margin:0;">
          <span style="width:8px;height:8px;border-radius:50%;
                        background:#1B4332;display:inline-block;"></span>
          Pendentes
          <span class="contagem-badge" id="cont-pendentes"
                style="<?= !$novos ? 'display:none' : '' ?>">
            <?= count($novos) ?>
          </span>
        </h2>
      </div>

      <div id="lista-pendentes">
        <?php if ($novos): ?>
          <?php foreach ($novos as $c):
            [$label, $classe] = $labels[$c['intencao']] ?? ['Outro','badge--cinza'];
          ?>
          <div class="contato-card novo"
               data-intencao="<?= htmlspecialchars($c['intencao']) ?>"
               data-secao="pendente"
               id="card-<?= $c['id'] ?>">
            <div>
              <div class="contato-card__topo">
                <div class="novo-dot"></div>
                <span class="contato-nome"><?= htmlspecialchars($c['nome']) ?></span>
                <a href="tel:<?= htmlspecialchars($c['telefone']) ?>"
                   class="contato-tel">
                  <?= htmlspecialchars($c['telefone']) ?>
                </a>
                <span class="badge <?= $classe ?>"><?= $label ?></span>
                <span class="contato-data">
                  <?= date('d/m/Y', strtotime($c['criado_em'])) ?>
                  às <?= date('H:i', strtotime($c['criado_em'])) ?>
                </span>
              </div>

              <div class="contato-card__info">
                <?php if ($c['tipo_criacao']): ?>
                <div class="info-item">
                  <label>Criação</label>
                  <span><?= htmlspecialchars($c['tipo_criacao']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($c['qtd_animais']): ?>
                <div class="info-item">
                  <label>Quantidade</label>
                  <span><?= htmlspecialchars($c['qtd_animais']) ?></span>
                </div>
                <?php endif; ?>
              </div>

              <?php if ($c['mensagem']): ?>
                <div class="contato-msg">
                  "<?= htmlspecialchars($c['mensagem']) ?>"
                </div>
              <?php endif; ?>
            </div>

            <div class="contato-acoes">
              <a href="https://wa.me/55<?= preg_replace('/\D/','',$c['telefone']) ?>?text=Olá+<?= urlencode($c['nome']) ?>%2C+recebi+sua+solicitação+e+gostaria+de+confirmar+o+agendamento."
                 target="_blank" class="btn-admin btn--verde btn--sm">
                WhatsApp
              </a>
              <button onclick="confirmarLido(<?= $c['id'] ?>, '<?= addslashes(htmlspecialchars($c['nome'])) ?>')"
                      class="btn-admin btn--outline btn--sm">
                Marcar atendido
              </button>
              <button onclick="confirmarDelete(<?= $c['id'] ?>, '<?= addslashes(htmlspecialchars($c['nome'])) ?>')"
                      class="btn-admin btn--perigo btn--sm">
                Remover
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <div class="estado-vazio" id="vazio-pendentes"
             style="<?= $novos ? 'display:none' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p>Nenhum contato pendente. Tudo em dia!</p>
        </div>

        <div class="secao-vazio-filtro" id="vazio-filtro-pendentes">
          Nenhum pendente nesta categoria.
        </div>
      </div>

      <!-- ── ATENDIDOS ── -->
      <?php if ($atendidos): ?>
      <div class="secao-titulo" onclick="toggleAtendidos()">
        <h2>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" style="width:15px;">
            <path d="M5 13l4 4L19 7"/>
          </svg>
          Atendidos / Visualizados
          <span class="badge badge--cinza"><?= count($atendidos) ?></span>
        </h2>
        <span class="chevron" id="chevron-atendidos">▼</span>
      </div>

      <div class="secao-conteudo" id="lista-atendidos">
        <div class="secao-vazio-filtro" id="vazio-filtro-atendidos">
          Nenhum atendido nesta categoria.
        </div>

        <?php foreach ($atendidos as $c):
          [$label, $classe] = $labels[$c['intencao']] ?? ['Outro','badge--cinza'];
        ?>
        <div class="contato-card lido"
             data-intencao="<?= htmlspecialchars($c['intencao']) ?>"
             data-secao="atendido">
          <div>
            <div class="contato-card__topo">
              <span class="contato-nome"
                    style="font-weight:500;">
                <?= htmlspecialchars($c['nome']) ?>
              </span>
              <a href="tel:<?= htmlspecialchars($c['telefone']) ?>"
                 class="contato-tel">
                <?= htmlspecialchars($c['telefone']) ?>
              </a>
              <span class="badge <?= $classe ?>"><?= $label ?></span>
              <span class="contato-data">
                <?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?>
              </span>
            </div>

            <div class="contato-card__info">
              <?php if ($c['tipo_criacao']): ?>
              <div class="info-item">
                <label>Criação</label>
                <span><?= htmlspecialchars($c['tipo_criacao']) ?></span>
              </div>
              <?php endif; ?>
              <?php if ($c['qtd_animais']): ?>
              <div class="info-item">
                <label>Quantidade</label>
                <span><?= htmlspecialchars($c['qtd_animais']) ?></span>
              </div>
              <?php endif; ?>
            </div>

            <?php if ($c['mensagem']): ?>
              <div class="contato-msg">
                "<?= htmlspecialchars($c['mensagem']) ?>"
              </div>
            <?php endif; ?>
          </div>

          <div class="contato-acoes">
            <button onclick="confirmarReabrir(<?= $c['id'] ?>, '<?= addslashes(htmlspecialchars($c['nome'])) ?>')"
                    class="btn-admin btn--outline btn--sm">
              Mover para pendentes
            </button>
            <button onclick="confirmarDelete(<?= $c['id'] ?>, '<?= addslashes(htmlspecialchars($c['nome'])) ?>')"
                    class="btn-admin btn--perigo btn--sm">
              Remover
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div>
  </main>
</div>

<!-- MODAL ATENDIDO -->
<div id="modal-lido" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
     z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:14px;padding:2rem;max-width:400px;width:92%;text-align:center;">
    <div style="width:52px;height:52px;background:#D1FAE5;border-radius:50%;
                display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#065F46" stroke-width="2.5" style="width:22px;">
        <path d="M5 13l4 4L19 7"/>
      </svg>
    </div>
    <h3 style="font-size:1rem;margin-bottom:.4rem;">Marcar como atendido?</h3>
    <p id="modal-lido-nome" style="font-size:.88rem;color:#555;font-weight:600;margin-bottom:.3rem;"></p>
    <p style="font-size:.78rem;color:#999;margin-bottom:1.5rem;">
      O contato será movido para "Atendidos / Visualizados".
    </p>
    <div style="display:flex;gap:.75rem;justify-content:center;">
      <button onclick="fecharModal('lido')" class="btn-admin btn--outline">Cancelar</button>
      <a id="link-lido" href="#" class="btn-admin btn--verde">Confirmar</a>
    </div>
  </div>
</div>

<!-- MODAL REABRIR -->
<div id="modal-reabrir" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
     z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:14px;padding:2rem;max-width:400px;width:92%;text-align:center;">
    <div style="width:52px;height:52px;background:#FEF3C7;border-radius:50%;
                display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#92400E" stroke-width="2" style="width:22px;">
        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
    </div>
    <h3 style="font-size:1rem;margin-bottom:.4rem;">Mover para pendentes?</h3>
    <p id="modal-reabrir-nome" style="font-size:.88rem;color:#555;font-weight:600;margin-bottom:.3rem;"></p>
    <p style="font-size:.78rem;color:#999;margin-bottom:1.5rem;">
      O contato voltará para a fila de pendentes.
    </p>
    <div style="display:flex;gap:.75rem;justify-content:center;">
      <button onclick="fecharModal('reabrir')" class="btn-admin btn--outline">Cancelar</button>
      <a id="link-reabrir" href="#" class="btn-admin btn--verde">Confirmar</a>
    </div>
  </div>
</div>

<!-- MODAL DELETE -->
<div id="modal-delete" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
     z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:14px;padding:2rem;max-width:400px;width:92%;text-align:center;">
    <div style="width:52px;height:52px;background:#FEE2E2;border-radius:50%;
                display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#991B1B" stroke-width="2" style="width:22px;">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
      </svg>
    </div>
    <h3 style="font-size:1rem;margin-bottom:.4rem;">Remover este contato?</h3>
    <p id="modal-delete-nome" style="font-size:.88rem;color:#555;font-weight:600;margin-bottom:.3rem;"></p>
    <p style="font-size:.78rem;color:#999;margin-bottom:1.5rem;">Esta ação não pode ser desfeita.</p>
    <div style="display:flex;gap:.75rem;justify-content:center;">
      <button onclick="fecharModal('delete')" class="btn-admin btn--outline">Cancelar</button>
      <a id="link-delete" href="#" class="btn-admin btn--perigo">Sim, remover</a>
    </div>
  </div>
</div>

<script src="/assets/js/filtros.js"></script>
<script>
// ── Filtro múltiplo ──────────────────────────────────────────────
const filtroAdmin = new FiltroMultiplo({
  container : '#filtros-admin',
  itens     : '.contato-card',
  atributo  : 'data-intencao',
  paramURL  : 'filtro',
  contador  : '#filtro-admin-contador',
});

// Sobrescreve _aplicar para atualizar contadores e vazios por seção
const _aplicarOriginal = filtroAdmin._aplicar.bind(filtroAdmin);
filtroAdmin._aplicar = function() {
  _aplicarOriginal();
  atualizarContadores();
  atualizarResumo();
};

function atualizarContadores() {
  const sel    = filtroAdmin.selecionados;
  const todos  = document.querySelectorAll('.contato-card');

  let pendentes = 0, atendidos = 0;
  todos.forEach(card => {
    const intencao = card.dataset.intencao;
    const visivel  = sel.size === 0 || sel.has(intencao);
    if (!visivel) return;
    if (card.dataset.secao === 'pendente') pendentes++;
    else atendidos++;
  });

  const badgePend = document.getElementById('cont-pendentes');
  if (badgePend) {
    badgePend.textContent = pendentes;
    badgePend.style.display = pendentes > 0 ? 'inline-flex' : 'none';
  }

  // Vazio pendentes (quando há filtro e nenhum resultado)
  const vazioFiltPend = document.getElementById('vazio-filtro-pendentes');
  const vazioNormal   = document.getElementById('vazio-pendentes');
  if (vazioFiltPend) {
    if (sel.size > 0 && pendentes === 0) {
      vazioFiltPend.style.display = 'block';
      if (vazioNormal) vazioNormal.style.display = 'none';
    } else {
      vazioFiltPend.style.display = 'none';
      if (vazioNormal) {
        const temPendentes = document.querySelectorAll('.contato-card.novo').length > 0;
        vazioNormal.style.display = (!temPendentes) ? 'block' : 'none';
      }
    }
  }

  // Vazio atendidos
  const vazioFiltAt = document.getElementById('vazio-filtro-atendidos');
  if (vazioFiltAt) {
    vazioFiltAt.style.display = (sel.size > 0 && atendidos === 0) ? 'block' : 'none';
  }

  // Botão limpar
  document.getElementById('btn-limpar').style.display = sel.size > 0 ? 'inline-block' : 'none';
}

function atualizarResumo() {
  const sel     = filtroAdmin.selecionados;
  const resumo  = document.getElementById('filtro-resumo');
  if (!resumo) return;

  const nomes = {
    clinica:'Clínica', consultoria:'Consultoria',
    procedimento:'Procedimento', senar:'SENAR', outro:'Outro'
  };

  if (sel.size > 0) {
    resumo.style.display = 'flex';
    resumo.innerHTML = '<span style="font-size:.72rem;color:var(--atxt2);">Mostrando:</span>';
    sel.forEach(v => {
      const tag = document.createElement('span');
      tag.className = 'filtro-tag-ativa';
      tag.textContent = nomes[v] || v;
      resumo.appendChild(tag);
    });
  } else {
    resumo.style.display = 'none';
  }
}

// ── Modais ───────────────────────────────────────────────────────
function confirmarLido(id, nome) {
  document.getElementById('modal-lido-nome').textContent = nome;
  document.getElementById('link-lido').href = '?lido=' + id;
  document.getElementById('modal-lido').style.display = 'flex';
}
function confirmarReabrir(id, nome) {
  document.getElementById('modal-reabrir-nome').textContent = nome;
  document.getElementById('link-reabrir').href = '?reabrir=' + id;
  document.getElementById('modal-reabrir').style.display = 'flex';
}
function confirmarDelete(id, nome) {
  document.getElementById('modal-delete-nome').textContent = nome;
  document.getElementById('link-delete').href = '?deletar=' + id;
  document.getElementById('modal-delete').style.display = 'flex';
}
function fecharModal(tipo) {
  document.getElementById('modal-' + tipo).style.display = 'none';
}
['modal-lido','modal-reabrir','modal-delete'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
  });
});

// ── Seção atendidos colapsável ───────────────────────────────────
function toggleAtendidos() {
  document.getElementById('lista-atendidos').classList.toggle('aberto');
  document.getElementById('chevron-atendidos').classList.toggle('aberto');
}

// ── Alerta some após 3s ──────────────────────────────────────────
const alerta = document.getElementById('alerta');
if (alerta) setTimeout(() => {
  alerta.style.transition = 'opacity .5s';
  alerta.style.opacity    = '0';
}, 3000);
</script>
</body>
</html>