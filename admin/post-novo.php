<?php
require_once __DIR__ . '/admin-auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$erro = '';

function uploadImagem(array $arquivo, string $prefixo = 'post'): string|false {
    $dir   = __DIR__ . '/../assets/img/posts/';
    $tipos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($arquivo['type'], $tipos)) return false;
    if ($arquivo['size'] > 5 * 1024 * 1024) return false;
    $ext  = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $nome = $prefixo . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($arquivo['tmp_name'], $dir . $nome)) return false;
    return '/assets/img/posts/' . $nome;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Upload AJAX de imagem para o conteúdo
    if (isset($_GET['acao']) && $_GET['acao'] === 'upload-imagem') {
        header('Content-Type: application/json');
        if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== 0) {
            echo json_encode(['erro' => 'Falha no envio.']); exit;
        }
        $url = uploadImagem($_FILES['imagem'], 'img');
        echo $url
            ? json_encode(['url' => $url])
            : json_encode(['erro' => 'Tipo ou tamanho inválido (máx. 5MB).']);
        exit;
    }

    $titulo    = sanitize($_POST['titulo']    ?? '');
    $resumo    = sanitize($_POST['resumo']    ?? '');
    $categoria = sanitize($_POST['categoria'] ?? 'manejo');
    $conteudo  = $_POST['conteudo'] ?? '';
    $publicado = isset($_POST['publicado']) ? 1 : 0;
    $capa      = '';

    if (!empty($_FILES['capa']['name']) && $_FILES['capa']['error'] === 0) {
        $url_capa = uploadImagem($_FILES['capa'], 'capa');
        if ($url_capa) $capa = $url_capa;
        else $erro = 'Erro no upload da capa. Use JPG, PNG ou WebP (máx. 5MB).';
    }

    if (!$erro && $titulo && $conteudo) {
        try {
            $slug = slugUnico($pdo, $titulo);
            $stmt = $pdo->prepare(
                "INSERT INTO posts (titulo, slug, resumo, conteudo, categoria, imagem, publicado)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$titulo, $slug, $resumo, $conteudo, $categoria, $capa, $publicado]);
            header('Location: /admin/posts.php?msg=criado');
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    } elseif (!$erro) {
        $erro = 'Título e conteúdo são obrigatórios.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Novo Post — Admin</title>
  <link rel="stylesheet" href="/admin/admin.css">
  <style>
    .editor-toolbar {
      display:flex;gap:.3rem;flex-wrap:wrap;align-items:center;padding:.6rem .75rem;
      background:var(--abg);border:1.5px solid var(--aborda);border-bottom:none;border-radius:8px 8px 0 0;
    }
    .toolbar-grupo{display:flex;gap:.25rem;}
    .toolbar-sep{width:1px;height:20px;background:var(--aborda);margin:0 .25rem;}
    .editor-toolbar button{padding:.3rem .55rem;border-radius:5px;font-size:.82rem;font-weight:600;cursor:pointer;background:#fff;border:1px solid var(--aborda);color:var(--atxt);transition:all .15s;line-height:1.2;}
    .editor-toolbar button:hover{background:#1B4332;color:#fff;border-color:#1B4332;}
    .btn-midia{display:inline-flex !important;align-items:center;gap:.35rem;font-size:.78rem !important;padding:.3rem .7rem !important;}
    #editor{min-height:340px;padding:1.25rem;outline:none;border:1.5px solid var(--aborda);border-radius:0 0 8px 8px;font-size:.93rem;line-height:1.8;color:var(--atxt);background:#fff;}
    #editor:empty::before{content:attr(data-placeholder);color:var(--atxt2);pointer-events:none;}
    #editor h2{font-size:1.35rem;color:#1B4332;margin:1.5rem 0 .5rem;}
    #editor h3{font-size:1.1rem;color:#1B4332;margin:1.25rem 0 .4rem;}
    #editor p{margin-bottom:1rem;}
    #editor blockquote{border-left:4px solid #52B788;padding:.6rem 1.25rem;background:#F0F7F4;border-radius:0 6px 6px 0;margin:1rem 0;font-style:italic;}
    #editor img{max-width:100%;border-radius:8px;margin:1rem 0;display:block;}
    #editor figure{margin:1.25rem 0;text-align:center;}
    #editor figcaption{font-size:.8rem;color:var(--atxt2);margin-top:.4rem;}
    #editor .video-wrapper{position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:8px;margin:1.25rem 0;}
    #editor .video-wrapper iframe{position:absolute;top:0;left:0;width:100%;height:100%;}
    .upload-capa{width:100%;aspect-ratio:16/9;border:2px dashed var(--aborda);border-radius:8px;cursor:pointer;overflow:hidden;transition:border-color .2s;display:flex;align-items:center;justify-content:center;}
    .upload-capa:hover{border-color:#1B4332;}
    .upload-capa__placeholder{display:flex;flex-direction:column;align-items:center;gap:.4rem;text-align:center;padding:1rem;color:var(--atxt2);}
    .upload-capa__placeholder span{font-size:.82rem;font-weight:600;}
    .upload-capa__placeholder small{font-size:.7rem;opacity:.7;line-height:1.5;}
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;}
    .modal-box{background:#fff;border-radius:14px;padding:1.75rem;max-width:520px;width:94%;max-height:88vh;overflow-y:auto;}
    .modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;}
    .modal-header h3{font-size:1rem;font-weight:600;}
    .modal-fechar{background:none;border:none;cursor:pointer;font-size:1.1rem;color:var(--atxt2);padding:.2rem .4rem;border-radius:4px;}
    .modal-fechar:hover{background:var(--abg);}
    .modal-tabs{display:flex;gap:.5rem;margin-bottom:1.25rem;border-bottom:1.5px solid var(--aborda);}
    .modal-tab{font-size:.83rem;font-weight:600;padding:.4rem .875rem;background:none;border:none;cursor:pointer;color:var(--atxt2);border-bottom:2.5px solid transparent;margin-bottom:-1.5px;transition:all .15s;}
    .modal-tab.ativo{color:#1B4332;border-color:#1B4332;}
    .upload-drop{border:2px dashed var(--aborda);border-radius:8px;padding:2rem;text-align:center;cursor:pointer;transition:all .2s;display:flex;flex-direction:column;align-items:center;gap:.5rem;color:var(--atxt2);}
    .upload-drop:hover,.upload-drop.ativo{border-color:#1B4332;background:#F0FDF4;}
    .upload-drop p{font-size:.85rem;margin:0;}
    .upload-drop small{font-size:.72rem;opacity:.7;}
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">
    <div class="topbar">
      <div style="display:flex;align-items:center;gap:.75rem;">
        <a href="/admin/posts.php" class="btn-admin btn--outline btn--sm">← Posts</a>
        <h1>Novo post</h1>
      </div>
    </div>
    <div class="content">
      <?php if ($erro): ?>
        <div class="alert alert--erro">✗ <?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <form method="POST" id="form-post" enctype="multipart/form-data">
        <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

          <div style="display:flex;flex-direction:column;gap:1.25rem;">
            <div class="card" style="padding:1.25rem;">
              <div class="campo">
                <label>Título *</label>
                <input type="text" name="titulo" required placeholder="Título do post"
                       value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                       style="font-size:1.1rem;font-weight:600;">
              </div>
              <div class="campo" style="margin-top:1rem;">
                <label>Resumo <span style="font-weight:400;color:var(--atxt2);">(aparece nos cards)</span></label>
                <textarea name="resumo" rows="2" placeholder="Descrição curta..."><?= htmlspecialchars($_POST['resumo'] ?? '') ?></textarea>
              </div>
            </div>

            <div class="card" style="padding:1.25rem;">
              <label style="font-size:.78rem;font-weight:600;color:var(--atxt2);display:block;margin-bottom:.75rem;">Conteúdo *</label>
              <div class="editor-toolbar">
                <div class="toolbar-grupo">
                  <button type="button" onclick="fmt('bold')"          title="Negrito"><b>N</b></button>
                  <button type="button" onclick="fmt('italic')"        title="Itálico"><i>I</i></button>
                  <button type="button" onclick="fmt('underline')"     title="Sublinhado"><u>S</u></button>
                  <button type="button" onclick="fmt('strikeThrough')" title="Tachado"><s>T</s></button>
                </div>
                <div class="toolbar-sep"></div>
                <div class="toolbar-grupo">
                  <button type="button" onclick="insTag('h2')"         title="Título H2">H2</button>
                  <button type="button" onclick="insTag('h3')"         title="Título H3">H3</button>
                  <button type="button" onclick="insTag('p')"          title="Parágrafo">§</button>
                  <button type="button" onclick="insTag('blockquote')" title="Destaque">❝</button>
                </div>
                <div class="toolbar-sep"></div>
                <div class="toolbar-grupo">
                  <button type="button" onclick="fmt('insertUnorderedList')" title="Lista">☰</button>
                  <button type="button" onclick="fmt('insertOrderedList')"   title="Numerada">1.</button>
                </div>
                <div class="toolbar-sep"></div>
                <div class="toolbar-grupo">
                  <button type="button" onclick="abrirModalImagem()" class="btn-midia">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    Imagem
                  </button>
                  <button type="button" onclick="abrirModalVideo()" class="btn-midia">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 10v4a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Vídeo
                  </button>
                </div>
                <div class="toolbar-sep"></div>
                <div class="toolbar-grupo">
                  <button type="button" onclick="fmt('removeFormat')" style="font-size:.72rem;color:var(--atxt2);">✕ Limpar</button>
                </div>
              </div>
              <div id="editor" contenteditable="true"
                   data-placeholder="Escreva o conteúdo do post aqui..."
                   oninput="sincEditor()">
                <?= !empty($_POST['conteudo']) ? $_POST['conteudo'] : '' ?>
              </div>
              <input type="hidden" name="conteudo" id="conteudo-hidden">
            </div>
          </div>

          <div style="display:flex;flex-direction:column;gap:1rem;position:sticky;top:80px;">
            <div class="card" style="padding:1.25rem;">
              <h3 style="font-size:.88rem;font-weight:600;margin-bottom:1rem;">Publicação</h3>
              <div class="campo">
                <label>Categoria</label>
                <select name="categoria">
                  <?php foreach (['manejo'=>'Manejo','gestao'=>'Gestão','saude'=>'Saúde animal','senar'=>'SENAR'] as $v=>$l): ?>
                    <option value="<?= $v ?>"><?= $l ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.85rem;margin-top:.875rem;">
                <input type="checkbox" name="publicado" checked> Publicar imediatamente
              </label>
              <div style="display:flex;flex-direction:column;gap:.5rem;margin-top:1.25rem;">
                <button type="submit" class="btn-admin btn--verde"
                        onclick="sincEditor()" style="width:100%;justify-content:center;">
                  Publicar post
                </button>
                <a href="/admin/posts.php" class="btn-admin btn--outline"
                   style="width:100%;justify-content:center;text-align:center;">Cancelar</a>
              </div>
            </div>

            <div class="card" style="padding:1.25rem;">
              <h3 style="font-size:.88rem;font-weight:600;margin-bottom:.875rem;">Imagem de capa</h3>
              <div class="upload-capa" onclick="document.getElementById('input-capa').click()">
                <div class="upload-capa__placeholder" id="capa-placeholder">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:28px;opacity:.4;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                  <span>Clique para adicionar<br>a imagem de capa</span>
                  <small>JPG, PNG ou WebP · Máx. 5MB<br>Recomendado: 1200×630px</small>
                </div>
                <img id="capa-preview" src="" alt=""
                     style="display:none;width:100%;height:100%;object-fit:cover;border-radius:8px;">
              </div>
              <input type="file" name="capa" id="input-capa"
                     accept="image/jpeg,image/png,image/webp"
                     style="display:none;" onchange="previewCapa(this)">
              <button type="button" id="btn-remover-capa" onclick="removerCapa()"
                      style="display:none;width:100%;margin-top:.6rem;"
                      class="btn-admin btn--perigo btn--sm">✕ Remover capa</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </main>
</div>

<!-- MODAL IMAGEM -->
<div id="modal-img" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Inserir imagem</h3>
      <button type="button" onclick="fecharModal('modal-img')" class="modal-fechar">✕</button>
    </div>
    <div class="modal-tabs">
      <button class="modal-tab ativo" onclick="trocarTab('tab-upload',this)">Upload</button>
      <button class="modal-tab"       onclick="trocarTab('tab-url',this)">URL externa</button>
    </div>
    <div id="tab-upload" class="modal-tab-conteudo">
      <div class="upload-drop" id="drop-area"
           ondrop="dropImagem(event)" ondragover="event.preventDefault()"
           ondragenter="this.classList.add('ativo')" ondragleave="this.classList.remove('ativo')"
           onclick="document.getElementById('input-img-editor').click()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:32px;opacity:.4;"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        <p>Arraste ou <strong>clique para selecionar</strong></p>
        <small>JPG, PNG, WebP · Máx. 5MB</small>
      </div>
      <input type="file" id="input-img-editor" accept="image/*" style="display:none;"
             onchange="uploadImagemEditor(this.files[0])">
      <div id="upload-progress" style="display:none;margin-top:.75rem;">
        <div style="height:4px;background:var(--aborda);border-radius:2px;">
          <div id="progress-bar" style="height:100%;background:#1B4332;border-radius:2px;width:0;transition:width .3s;"></div>
        </div>
        <p style="font-size:.75rem;color:var(--atxt2);margin-top:.4rem;" id="upload-status">Enviando...</p>
      </div>
      <div id="img-preview-upload" style="display:none;margin-top:.75rem;">
        <img id="img-preview-src" src="" alt="" style="width:100%;border-radius:8px;max-height:200px;object-fit:cover;">
      </div>
    </div>
    <div id="tab-url" class="modal-tab-conteudo" style="display:none;">
      <div class="campo">
        <label>URL da imagem</label>
        <input type="text" id="input-url-img" placeholder="https://exemplo.com/imagem.jpg">
      </div>
      <div id="url-img-preview" style="display:none;margin-top:.75rem;">
        <img id="url-img-preview-src" src="" alt="" style="width:100%;border-radius:8px;max-height:200px;object-fit:cover;">
      </div>
    </div>
    <div class="campo" style="margin-top:1rem;">
      <label>Legenda <span style="font-weight:400;color:var(--atxt2);">(opcional)</span></label>
      <input type="text" id="input-legenda-img" placeholder="Descrição da imagem...">
    </div>
    <div style="display:flex;gap:.75rem;margin-top:1.25rem;">
      <button type="button" class="btn-admin btn--verde"   onclick="inserirImagem()">Inserir</button>
      <button type="button" class="btn-admin btn--outline" onclick="fecharModal('modal-img')">Cancelar</button>
    </div>
  </div>
</div>

<!-- MODAL VÍDEO -->
<div id="modal-video" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Inserir vídeo</h3>
      <button type="button" onclick="fecharModal('modal-video')" class="modal-fechar">✕</button>
    </div>
    <div class="campo">
      <label>URL do vídeo</label>
      <input type="text" id="input-url-video" placeholder="Cole o link do YouTube ou Vimeo"
             oninput="previewVideo(this.value)">
      <span style="font-size:.72rem;color:var(--atxt2);margin-top:.3rem;display:block;">Suportado: YouTube e Vimeo</span>
    </div>
    <div id="video-preview" style="display:none;margin-top:1rem;border-radius:8px;overflow:hidden;aspect-ratio:16/9;background:#000;">
      <iframe id="video-iframe" src="" frameborder="0" allowfullscreen style="width:100%;height:100%;"></iframe>
    </div>
    <div style="background:#FEF3C7;border:1px solid #FCD34D;border-radius:8px;padding:.75rem 1rem;margin-top:1rem;font-size:.78rem;color:#78350F;">
      <strong>Dica:</strong> vídeos de manejo e procedimentos aumentam o tempo de permanência no site.
    </div>
    <div style="display:flex;gap:.75rem;margin-top:1.25rem;">
      <button type="button" class="btn-admin btn--verde"   onclick="inserirVideo()">Inserir</button>
      <button type="button" class="btn-admin btn--outline" onclick="fecharModal('modal-video')">Cancelar</button>
    </div>
  </div>
</div>

<script>
const editor = document.getElementById('editor');
function fmt(cmd) { editor.focus(); document.execCommand(cmd, false, null); }
function insTag(tag) {
  editor.focus();
  const sel = window.getSelection();
  const t   = sel && sel.toString() ? sel.toString() : 'Texto aqui';
  document.execCommand('insertHTML', false, `<${tag}>${t}</${tag}><p></p>`);
}
function sincEditor() { document.getElementById('conteudo-hidden').value = editor.innerHTML; }
document.getElementById('form-post').addEventListener('submit', sincEditor);

function previewCapa(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('capa-preview').src = e.target.result;
    document.getElementById('capa-preview').style.display = 'block';
    document.getElementById('capa-placeholder').style.display = 'none';
    document.getElementById('btn-remover-capa').style.display = 'block';
  };
  reader.readAsDataURL(input.files[0]);
}
function removerCapa() {
  document.getElementById('input-capa').value = '';
  document.getElementById('capa-preview').style.display = 'none';
  document.getElementById('capa-placeholder').style.display = 'flex';
  document.getElementById('btn-remover-capa').style.display = 'none';
}

function abrirModalImagem() { sincEditor(); salvarSelecao(); document.getElementById('modal-img').style.display='flex'; }
function abrirModalVideo()  { sincEditor(); salvarSelecao(); document.getElementById('modal-video').style.display='flex'; }
function fecharModal(id)    { document.getElementById(id).style.display='none'; }
['modal-img','modal-video'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) { if (e.target===this) fecharModal(id); });
});
function trocarTab(id, btn) {
  document.querySelectorAll('.modal-tab-conteudo').forEach(t => t.style.display='none');
  document.querySelectorAll('.modal-tab').forEach(b => b.classList.remove('ativo'));
  document.getElementById(id).style.display = 'block';
  btn.classList.add('ativo');
}

let selecaoSalva = null;
function salvarSelecao() {
  const sel = window.getSelection();
  if (sel && sel.rangeCount > 0) selecaoSalva = sel.getRangeAt(0).cloneRange();
}
function restaurarSelecao() {
  if (!selecaoSalva) return;
  const sel = window.getSelection();
  sel.removeAllRanges();
  sel.addRange(selecaoSalva);
}

let urlImagemPendente = '';
function uploadImagemEditor(file) {
  if (!file) return;
  const fd = new FormData();
  fd.append('imagem', file);
  document.getElementById('upload-progress').style.display = 'block';
  document.getElementById('progress-bar').style.width = '30%';
  document.getElementById('upload-status').textContent = 'Enviando...';
  fetch('?acao=upload-imagem', { method:'POST', body:fd })
    .then(r => r.json())
    .then(data => {
      document.getElementById('progress-bar').style.width = '100%';
      if (data.erro) { document.getElementById('upload-status').textContent = '✗ '+data.erro; return; }
      urlImagemPendente = data.url;
      document.getElementById('upload-status').textContent = '✓ Upload concluído!';
      document.getElementById('img-preview-upload').style.display = 'block';
      document.getElementById('img-preview-src').src = data.url;
    })
    .catch(() => { document.getElementById('upload-status').textContent = '✗ Erro no upload.'; });
}
function dropImagem(e) {
  e.preventDefault();
  document.getElementById('drop-area').classList.remove('ativo');
  const f = e.dataTransfer.files[0];
  if (f && f.type.startsWith('image/')) uploadImagemEditor(f);
}
document.getElementById('input-url-img').addEventListener('input', function() {
  const url = this.value.trim();
  document.getElementById('url-img-preview').style.display = url ? 'block' : 'none';
  if (url) document.getElementById('url-img-preview-src').src = url;
});
document.getElementById('input-img-editor').addEventListener('change', function() {
  if (this.files[0]) uploadImagemEditor(this.files[0]);
});
function inserirImagem() {
  const tabAtiva = document.querySelector('.modal-tab.ativo').textContent.trim();
  const legenda  = document.getElementById('input-legenda-img').value.trim();
  const url = tabAtiva === 'Upload'
    ? urlImagemPendente
    : document.getElementById('input-url-img').value.trim();
  if (!url) { alert('Selecione ou informe a URL de uma imagem.'); return; }
  restaurarSelecao(); editor.focus();
  const html = legenda
    ? `<figure><img src="${url}" alt="${legenda}"><figcaption>${legenda}</figcaption></figure><p></p>`
    : `<img src="${url}" alt=""><p></p>`;
  document.execCommand('insertHTML', false, html);
  sincEditor(); fecharModal('modal-img');
  urlImagemPendente = '';
  document.getElementById('input-legenda-img').value = '';
  document.getElementById('upload-progress').style.display = 'none';
  document.getElementById('img-preview-upload').style.display = 'none';
  document.getElementById('url-img-preview').style.display = 'none';
  document.getElementById('input-url-img').value = '';
  document.getElementById('progress-bar').style.width = '0';
}

function extrairEmbedURL(url) {
  const yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  if (yt) return `https://www.youtube.com/embed/${yt[1]}?rel=0`;
  const vimeo = url.match(/vimeo\.com\/(\d+)/);
  if (vimeo) return `https://player.vimeo.com/video/${vimeo[1]}`;
  return '';
}
function previewVideo(url) {
  const embed = extrairEmbedURL(url.trim());
  const prev  = document.getElementById('video-preview');
  if (embed) { document.getElementById('video-iframe').src = embed; prev.style.display = 'block'; }
  else prev.style.display = 'none';
}
function inserirVideo() {
  const url   = document.getElementById('input-url-video').value.trim();
  const embed = extrairEmbedURL(url);
  if (!embed) { alert('URL inválida. Use YouTube ou Vimeo.'); return; }
  restaurarSelecao(); editor.focus();
  document.execCommand('insertHTML', false,
    `<div class="video-wrapper"><iframe src="${embed}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe></div><p></p>`
  );
  sincEditor(); fecharModal('modal-video');
  document.getElementById('input-url-video').value = '';
  document.getElementById('video-preview').style.display = 'none';
  document.getElementById('video-iframe').src = '';
}
</script>
</body>
</html>