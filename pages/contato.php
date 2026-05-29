<?php
$page_title = 'Agendar visita — Tamires Müller Veterinária';
$page_desc  = 'Solicite uma visita técnica ou saiba mais sobre a parceria SENAR. Atendimento em São Martinho e região.';

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../includes/header.php';

$intencao_pre = sanitize($_GET['intencao'] ?? '');
$validas = ['clinica', 'consultoria', 'procedimento', 'senar', 'outro'];
if (!in_array($intencao_pre, $validas)) $intencao_pre = '';
?>

<section class="secao">
  <div class="container">

    <div class="reveal" style="max-width:640px;">
      <span class="tag">Agende sua visita</span>
      <h1 class="titulo-secao">Solicite atendimento na sua propriedade</h1>
      <p class="subtitulo-secao">
        Preencha o formulário abaixo. Tamires entrará em contato para confirmar o agendamento conforme disponibilidade.
      </p>
    </div>

    <div class="contato__grid" style="margin-top:2.5rem;">

      <!-- ESQUERDA — informações -->
      <div class="contato__info reveal">

        <div class="info-bloco">
          <div class="info-icone">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
          </div>
          <div>
            <h3>Prefere o WhatsApp?</h3>
            <p>Para situações de urgência ou dúvidas rápidas, fale direto pelo WhatsApp.</p>
            <a href="https://wa.me/5554999999999?text=Olá%20Tamires%2C%20preciso%20de%20atendimento%20veterinário."
               target="_blank" class="btn btn--primario" style="margin-top:.75rem;font-size:.85rem;">
              Chamar no WhatsApp
            </a>
          </div>
        </div>

        <div class="info-bloco">
          <div class="info-icone">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div>
            <h3>Área de atendimento</h3>
            <p>São Martinho, Boa Vista do Buricá, Sede Nova, Nova Candelária e região — raio aproximado de 30 km.</p>
          </div>
        </div>

        <div class="info-bloco">
          <div class="info-icone">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <path d="M12 8v4l3 3"/>
            </svg>
          </div>
          <div>
            <h3>Como funciona</h3>
            <ol class="como-funciona">
              <li><span>1</span> Você preenche o formulário</li>
              <li><span>2</span> Tamires confirma pelo WhatsApp</li>
              <li><span>3</span> Visita agendada na sua propriedade</li>
            </ol>
          </div>
        </div>

        <div class="nao-atende">
          <h4>Especialidade principal</h4>
          <p>Bovinos leiteiros e gerenciamento de propriedades rurais. Para outras espécies, consulte disponibilidade.</p>
          <ul>
            <li>Clínica e medicina preventiva bovina</li>
            <li>Consultoria de gestão rural</li>
            <li>Manejos sanitários do rebanho</li>
            <li>Mochamento e casqueamento</li>
            <li>Parceria gratuita via SENAR</li>
          </ul>
        </div>

      </div>

      <!-- DIREITA — formulário -->
      <div class="reveal">
        <div class="form-card">

          <p style="font-size:.8rem;font-weight:600;color:var(--cinza-escuro);margin-bottom:.75rem;">
            Qual é o motivo da visita? *
          </p>

          <div class="intencao-grid" id="intencao-grid">
            <?php
            $opcoes = [
              'clinica'      => ['Atendimento clínico',    'Animal doente, ferido ou sintomas a investigar.'],
              'consultoria'  => ['Consultoria de gestão',  'Planejamento e gerenciamento da propriedade.'],
              'procedimento' => ['Procedimento cirúrgico', 'Mochamento, casqueamento ou outra intervenção.'],
              'senar'        => ['Parceria SENAR',          'Visita mensal gratuita pelo programa SENAR.'],
            ];
            foreach ($opcoes as $val => [$titulo, $desc]):
              $ativo = $intencao_pre === $val ? 'ativo' : '';
            ?>
            <button type="button"
                    class="intencao-btn <?= $ativo ?>"
                    data-val="<?= $val ?>"
                    onclick="selecionarIntencao('<?= $val ?>')">
              <strong><?= $titulo ?></strong>
              <span><?= $desc ?></span>
            </button>
            <?php endforeach; ?>
          </div>

          <form id="form-contato" style="margin-top:1.5rem;">
            <input type="hidden" name="intencao" id="campo-intencao"
                   value="<?= htmlspecialchars($intencao_pre ?: '') ?>">

            <!-- Nome e Telefone -->
            <div class="form__linha">
              <div class="form__grupo">
                <label>Nome completo *</label>
                <input type="text" name="nome" required placeholder="Seu nome">
              </div>
              <div class="form__grupo">
                <label>Telefone / WhatsApp *</label>
                <input type="tel" name="telefone" required
                       placeholder="(54) 9 9999-9999"
                       oninput="mascaraTel(this)">
              </div>
            </div>

            <!-- Endereço -->
            <div class="form__linha">
              <div class="form__grupo">
                <label>Município / Localidade *</label>
                <input type="text" name="municipio" required
                       placeholder="Ex: São Martinho — Linha Rolador">
              </div>
              <div class="form__grupo">
                <label>Distância aproximada da cidade</label>
                <select name="distancia">
                  <option value="">Selecione...</option>
                  <option value="Até 10km">Até 10 km</option>
                  <option value="10-20km">10 a 20 km</option>
                  <option value="20-30km">20 a 30 km</option>
                  <option value="Mais de 30km">Mais de 30 km</option>
                </select>
              </div>
            </div>

            <!-- Tipo de criação e quantidade -->
            <div class="form__linha">
              <div class="form__grupo">
                <label>Tipo de criação *</label>
                <select name="tipo_criacao" required>
                  <option value="">Selecione...</option>
                  <option value="Bovinos leiteiros">Bovinos leiteiros</option>
                  <option value="Bovinos de corte">Bovinos de corte</option>
                  <!-- <option value="Suínos">Suínos</option> -->
                  <!-- <option value="Aves">Aves</option> -->
                  <option value="Misto">Misto (bovinos + outros)</option>
                  <option value="Outro">Outro</option>
                </select>
              </div>
              <div class="form__grupo">
                <label>Quantidade de cabeças *</label>
                <select name="qtd_animais" required>
                  <option value="">Selecione...</option>
                  <option value="1-10">1 a 10 cabeças</option>
                  <option value="11-30">11 a 30 cabeças</option>
                  <option value="31-50">31 a 50 cabeças</option>
                  <option value="51-100">51 a 100 cabeças</option>
                  <option value="100+">Mais de 100 cabeças</option>
                </select>
              </div>
            </div>

            <!-- Descrição do problema — aparece apenas para clínica/procedimento -->
            <div class="form__grupo" id="grupo-animal" style="display:none;">
              <label>Espécie e sintomas / procedimento desejado</label>
              <input type="text" name="animal_desc"
                     placeholder="Ex: vaca — febre há 2 dias / terneiro — mochamento">
            </div>

            <!-- Mensagem -->
            <div class="form__grupo">
              <label>Observações adicionais</label>
              <textarea name="mensagem" rows="3"
                placeholder="Qualquer informação que ajude no atendimento..."></textarea>
            </div>

            <div id="form-aviso" class="form__aviso" role="alert" aria-live="polite"></div>

            <button type="submit" id="btn-submit" class="btn btn--primario">
              Enviar solicitação de visita
            </button>

            <p class="form-nota">
              Seus dados são usados apenas para agendamento. Retorno em até 24 horas.
            </p>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<style>
.info-bloco {
  display:flex; gap:1rem; align-items:flex-start;
  padding:1.5rem 0; border-bottom:1px solid rgba(0,0,0,.07);
}
.info-bloco:last-of-type { border-bottom:none; }
.info-icone {
  width:48px; height:48px; border-radius:var(--raio-sm);
  background:var(--verde-suave); display:flex;
  align-items:center; justify-content:center; flex-shrink:0;
}
.info-icone svg { width:22px; color:var(--verde-medio); }
.info-bloco h3 { font-size:1.1rem; color:var(--verde-escuro); margin-bottom:.4rem; }
.info-bloco p  { font-size:.97rem; color:var(--cinza-medio); line-height:1.6; }

.como-funciona { list-style:none; padding:0; display:flex; flex-direction:column; gap:.6rem; margin-top:.6rem; }
.como-funciona li { display:flex; align-items:center; gap:.75rem; font-size:.97rem; color:var(--cinza-escuro); }
.como-funciona li span {
  width:28px; height:28px; border-radius:50%;
  background:var(--verde-escuro); color:#fff;
  font-size:.8rem; font-weight:700;
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.nao-atende {
  background:var(--verde-suave); border-radius:var(--raio-md);
  padding:1.4rem; margin-top:1.25rem;
}
.nao-atende h4 { font-size:.97rem; color:var(--verde-escuro); margin-bottom:.5rem; }
.nao-atende p  { font-size:.95rem; color:var(--cinza-escuro); margin-bottom:.875rem; line-height:1.6; }
.nao-atende ul { list-style:none; padding:0; display:flex; flex-direction:column; gap:.45rem; }
.nao-atende ul li { font-size:.95rem; color:var(--verde-medio); display:flex; align-items:center; gap:.5rem; }
.nao-atende ul li::before { content:''; width:7px; height:7px; border-radius:50%; background:var(--verde-claro); flex-shrink:0; }

.form-card {
  background:#fff; border:1px solid rgba(0,0,0,.08);
  border-radius:var(--raio-lg); padding:2rem; box-shadow:var(--sombra-sm);
}
.intencao-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
.intencao-btn {
  background:var(--cinza-claro); border:2px solid transparent;
  border-radius:var(--raio-sm); padding:1rem; text-align:left;
  cursor:pointer; transition:all .2s; display:flex; flex-direction:column; gap:.3rem;
}
.intencao-btn strong { font-size:.97rem; color:var(--preto); display:block; }
.intencao-btn span   { font-size:.84rem; color:var(--cinza-medio); display:block; line-height:1.4; }
.intencao-btn:hover  { border-color:var(--verde-claro); background:var(--verde-suave); }
.intencao-btn.ativo  { border-color:var(--verde-escuro); background:var(--verde-suave); }
.intencao-btn.ativo strong { color:var(--verde-escuro); }

/* Inputs e selects maiores */
.form__grupo label { font-size:1rem; font-weight:600; color:var(--cinza-escuro); margin-bottom:.4rem; display:block; }
.form__grupo input,
.form__grupo select,
.form__grupo textarea {
  font-size:1rem;
  padding:.85rem 1rem;
  border-radius:var(--raio-sm);
  border:1.5px solid rgba(0,0,0,.15);
  width:100%;
  line-height:1.5;
}
.form__grupo input:focus,
.form__grupo select:focus,
.form__grupo textarea:focus {
  outline:none;
  border-color:var(--verde-escuro);
  box-shadow:0 0 0 3px rgba(44,103,64,.12);
}

/* Feedback do formulário */
.form__aviso {
  display:none;
  border-radius:var(--raio-sm);
  padding:1rem 1.25rem;
  font-size:1rem;
  font-weight:500;
  line-height:1.5;
  margin-top:.75rem;
  align-items:flex-start;
  gap:.75rem;
}
.form__aviso.sucesso {
  display:flex;
  background:#d1fae5;
  border:1.5px solid #34d399;
  color:#065f46;
}
.form__aviso.erro {
  display:flex;
  background:#fee2e2;
  border:1.5px solid #f87171;
  color:#991b1b;
}
.form__aviso.carregando {
  display:flex;
  background:#f0fdf4;
  border:1.5px solid var(--verde-claro);
  color:var(--verde-escuro);
}
.form__aviso__icone { font-size:1.3rem; flex-shrink:0; margin-top:.05rem; }
.form__aviso__texto strong { display:block; margin-bottom:.2rem; }

/* Botão de submit maior */
#btn-submit {
  width:100%; justify-content:center; margin-top:.75rem;
  padding:1.1rem; font-size:1.05rem; letter-spacing:.01em;
}
#btn-submit:disabled { opacity:.6; cursor:not-allowed; }

/* Nota de rodapé do form */
.form-nota {
  font-size:.85rem; color:var(--cinza-medio);
  text-align:center; margin-top:.875rem; line-height:1.5;
}

/* Hero textos maiores */
.subtitulo-secao { font-size:1.05rem !important; }

@media(max-width:600px) {
  .intencao-grid { grid-template-columns:1fr; }
  .form-card { padding:1.25rem; }
}
</style>

<script>
/* ── Intenção ── */
function selecionarIntencao(val) {
  document.querySelectorAll('.intencao-btn').forEach(b => b.classList.remove('ativo'));
  const btn = document.querySelector(`[data-val="${val}"]`);
  if (btn) btn.classList.add('ativo');
  document.getElementById('campo-intencao').value = val;

  const grupoAnimal = document.getElementById('grupo-animal');
  grupoAnimal.style.display = (val === 'clinica' || val === 'procedimento') ? 'flex' : 'none';
}

/* ── Máscara telefone ── */
function mascaraTel(input) {
  let v = input.value.replace(/\D/g,'').slice(0,11);
  if (v.length > 6) v = `(${v.slice(0,2)}) ${v.slice(2,7)}-${v.slice(7)}`;
  else if (v.length > 2) v = `(${v.slice(0,2)}) ${v.slice(2)}`;
  input.value = v;
}

/* ── Feedback visual ── */
function mostrarAviso(tipo, titulo, mensagem) {
  const el = document.getElementById('form-aviso');
  const icones = { sucesso: '✅', erro: '❌', carregando: '⏳' };
  el.className = `form__aviso ${tipo}`;
  el.innerHTML = `
    <span class="form__aviso__icone">${icones[tipo]}</span>
    <div class="form__aviso__texto">
      <strong>${titulo}</strong>
      ${mensagem}
    </div>`;
  el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function ocultarAviso() {
  const el = document.getElementById('form-aviso');
  el.className = 'form__aviso';
  el.innerHTML = '';
}

/* ── Submit ── */
document.addEventListener('DOMContentLoaded', () => {

  // Pré-seleção pela URL
  const val = document.getElementById('campo-intencao').value;
  if (val) selecionarIntencao(val);

  const form   = document.getElementById('form-contato');
  const btnEnv = document.getElementById('btn-submit');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    ocultarAviso();

    // Validação: intenção obrigatória
    const intencao = document.getElementById('campo-intencao').value;
    if (!intencao) {
      mostrarAviso('erro',
        'Selecione o motivo da visita.',
        'Toque em uma das opções acima antes de enviar.');
      document.getElementById('intencao-grid').scrollIntoView({ behavior:'smooth', block:'center' });
      return;
    }

    // Estado de carregamento
    btnEnv.disabled = true;
    btnEnv.textContent = 'Enviando…';
    mostrarAviso('carregando', 'Enviando sua solicitação…', 'Aguarde um momento.');

    try {
      const resp = await fetch('/api/contato.php', {
        method : 'POST',
        body   : new FormData(form),
      });

      const data = await resp.json().catch(() => null);

      if (resp.ok && data?.sucesso) {
        // ── SUCESSO ──
        mostrarAviso('sucesso',
          'Solicitação enviada com sucesso! 🎉',
          `Tamires entrará em contato pelo WhatsApp em até 24 horas para confirmar o agendamento.
           <br><br>Fique de olho no número <strong>(54) 9 9999-9999</strong>.`
        );
        form.reset();
        ocultarBotao();
        // Reabilita após 8s para reenvio caso o produtor queira
        setTimeout(() => {
          btnEnv.disabled = false;
          btnEnv.textContent = 'Enviar nova solicitação';
        }, 8000);
      } else {
        // ── ERRO DO SERVIDOR ──
        const msg = data?.mensagem || 'Não foi possível enviar agora.';
        mostrarAviso('erro',
          'Algo deu errado.',
          `${msg}<br><br>Se o problema persistir, entre em contato diretamente pelo
           <a href="https://wa.me/5554999999999" target="_blank" style="color:inherit;font-weight:700;text-decoration:underline;">
             WhatsApp ↗
           </a>.`
        );
        btnEnv.disabled = false;
        btnEnv.textContent = 'Tentar novamente';
      }
    } catch (err) {
      // ── SEM CONEXÃO ──
      mostrarAviso('erro',
        'Sem conexão com a internet.',
        `Verifique o sinal do celular e tente novamente.<br><br>Ou fale direto pelo
         <a href="https://wa.me/5554999999999" target="_blank" style="color:inherit;font-weight:700;text-decoration:underline;">
           WhatsApp ↗
         </a>.`
      );
      btnEnv.disabled = false;
      btnEnv.textContent = 'Tentar novamente';
    }
  });

  function ocultarBotao() {
    btnEnv.textContent = '✅ Enviado';
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>