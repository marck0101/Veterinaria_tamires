<?php
$page_title = 'Sobre — Tamires Müller, Médica Veterinária';
$page_desc  = 'Conheça a trajetória de Tamires Laís Müller, médica veterinária especializada em bovinos leiteiros, filha de produtor rural e parceira SENAR na região de São Martinho, RS.';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- HERO SOBRE -->
<section class="secao" style="padding-bottom:0;">
  <div class="container">
    <div class="sobre-hero reveal">
      <div class="sobre-hero__texto">
        <span class="tag">Quem sou eu</span>
        <h1 class="titulo-secao" style="font-size:clamp(2rem,4vw,3rem);">
          Tamires Laís Müller
        </h1>
        <p class="sobre-hero__cargo">Médica Veterinária — CRMV/RS</p>
        <p class="sobre-hero__desc">
          Cresci em uma granja leiteira em São Martinho, RS. Desde pequena acompanhei de perto a rotina, os desafios e as alegrias de quem vive no campo. Essa vivência moldou minha escolha pela medicina veterinária — não como carreira distante, mas como forma de devolver ao produtor rural o que aprendi crescendo entre eles.
        </p>
        <p class="sobre-hero__desc">
          Hoje atuo na clínica de bovinos leiteiros e no gerenciamento de propriedades rurais, com foco em resultados práticos. Não trago só o diagnóstico — trago um plano de ação construído junto com o produtor.
        </p>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:1.75rem;">
          <a href="/contato?intencao=clinica" class="btn btn--primario">Solicitar visita</a>
          <a href="https://wa.me/5555969595660" target="_blank" class="btn btn--outline">WhatsApp</a>
        </div>
      </div>
      <div class="sobre-hero__foto">
        <img
          src="/assets/img/posts/sobre.png"
          alt="Tamires Laís Müller — Médica Veterinária, CRMV/RS"
          width="798" height="480"
          loading="eager"
          fetchpriority="high"
          decoding="async"
          class="sobre-hero__img"
        >
        <div class="sobre-hero__cards">
          <div class="mini-card">
            <strong>IFFar</strong>
            <span>Medicina Veterinária — 2025</span>
          </div>
          <div class="mini-card">
            <strong>SENAR</strong>
            <span>Parceira ATeG</span>
          </div>
          <div class="mini-card">
            <strong>Pós-grad.</strong>
            <span>Clínica Bovina — 2026</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FRASE DE IMPACTO -->
<section style="background:var(--verde-escuro);padding:3rem 0;margin-top:3rem;">
  <div class="container" style="max-width:760px;text-align:center;">
    <blockquote class="reveal" style="font-family:var(--fonte-titulo);font-size:clamp(1.3rem,3vw,1.9rem);
      color:#fff;line-height:1.4;font-style:italic;border:none;padding:0;">
      "Acredito no trabalho próximo ao produtor, no acompanhamento contínuo e na construção de soluções junto com quem está no campo."
    </blockquote>
  </div>
</section>

<!-- TRAJETÓRIA -->
<section class="secao">
  <div class="container">
    <div class="reveal" style="text-align:center;max-width:600px;margin-inline:auto;margin-bottom:3rem;">
      <span class="tag">Trajetória</span>
      <h2 class="titulo-secao">Experiência construída no campo</h2>
      <p class="subtitulo-secao" style="margin-inline:auto;">
        De filha de produtor a médica veterinária — cada etapa reforçou o compromisso com a pecuária leiteira.
      </p>
    </div>

    <div class="timeline">

      <div class="timeline-item reveal">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <span class="timeline-periodo">Mar/2025 — atual</span>
          <h3>Médica Veterinária — Granja Follmann</h3>
          <p class="timeline-local">Boa Vista do Buricá, RS</p>
          <p>Identificação e tratamento de animais, manejos sanitários e biosseguridade, acompanhamento reprodutivo, controle de qualidade do leite e treinamento de equipe. Responsável por coleta e análise de dados produtivos.</p>
        </div>
      </div>

      <div class="timeline-item reveal">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <span class="timeline-periodo">Set/2024 — Jan/2025</span>
          <h3>Estagiária — Agroveterinária Rockenbach</h3>
          <p class="timeline-local">São Martinho, RS</p>
          <p>Exames clínicos, administração de medicamentos, coletas biológicas para envio laboratorial e manejos reprodutivos e sanitários.</p>
        </div>
      </div>

      <div class="timeline-item reveal">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <span class="timeline-periodo">Mar/2023 — Jul/2024</span>
          <h3>Estagiária — Lab. Cirurgia Veterinária, IFFar</h3>
          <p class="timeline-local">Frederico Westphalen, RS</p>
          <p>Apoio ao bloco cirúrgico, auxílio em procedimentos e monitoramento anestésico. Monitoria nas disciplinas de Anestesiologia, Técnica Cirúrgica e Cirurgia Veterinária.</p>
        </div>
      </div>

      <div class="timeline-item reveal">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <span class="timeline-periodo">Jan/2024 — Fev/2024</span>
          <h3>Estagiária — Agropecuária Klockner</h3>
          <p class="timeline-local">Humaitá, RS</p>
          <p>Atendimentos e procedimentos veterinários com foco em manejos reprodutivos.</p>
        </div>
      </div>

      <div class="timeline-item reveal">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <span class="timeline-periodo">2016 — 2024</span>
          <h3>Produtora Rural — Granja Müller</h3>
          <p class="timeline-local">São Martinho, RS</p>
          <p>Ordenha, exames clínicos, protocolos de IATF, inseminação artificial, manejos sanitários e preventivos. Gestão financeira da propriedade incluindo controle de fluxo de caixa e compras de insumos.</p>
          <span class="timeline-badge">Base da minha visão de campo</span>
        </div>
      </div>

      <div class="timeline-item reveal">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <span class="timeline-periodo">Set/2021 — Dez/2021</span>
          <h3>Estagiária — Assistência Reprodutiva Fernando Reimann</h3>
          <p class="timeline-local">São Martinho, RS</p>
          <p>Manejos reprodutivos e atendimentos veterinários em bovinos.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- FORMAÇÃO -->
<section class="secao secao--alt">
  <div class="container">
    <div class="reveal" style="text-align:center;max-width:560px;margin-inline:auto;margin-bottom:2.5rem;">
      <span class="tag">Formação</span>
      <h2 class="titulo-secao">Acadêmica e técnica</h2>
    </div>

    <div class="formacao-grid">
      <div class="formacao-card reveal">
        <div class="formacao-icone">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
          </svg>
        </div>
        <div>
          <span class="formacao-ano">2026 — Em conclusão</span>
          <h3>Pós-graduação em Clínica Médica e Cirúrgica de Bovinos</h3>
          <p>Grupo PPGVET — Educação à Distância</p>
        </div>
      </div>

      <div class="formacao-card reveal">
        <div class="formacao-icone">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
          </svg>
        </div>
        <div>
          <span class="formacao-ano">2025 — Concluído</span>
          <h3>Bacharelado em Medicina Veterinária</h3>
          <p>Instituto Federal Farroupilha — Frederico Westphalen, RS</p>
        </div>
      </div>

      <div class="formacao-card reveal">
        <div class="formacao-icone">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="7" width="20" height="14" rx="2"/>
            <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
          </svg>
        </div>
        <div>
          <span class="formacao-ano">2019 — Concluído</span>
          <h3>Técnico em Agropecuária</h3>
          <p>Instituto Federal Farroupilha — Santo Augusto, RS</p>
        </div>
      </div>
    </div>

    <!-- Cursos SENAR -->
    <div class="reveal" style="margin-top:3rem;">
      <h3 style="font-size:1.1rem;color:var(--verde-escuro);margin-bottom:1.25rem;text-align:center;">
        Capacitações e cursos
      </h3>
      <div class="cursos-grid">
        <?php
        $cursos = [
          ['Farmácia Veterinária na Propriedade Rural', 'SENAR', '2024'],
          ['Cirurgia em Animais de Produção',           'Tambo Cestonaro', '2023'],
          ['Criação de Bezerras',                        'Bezerrinsta', '2022'],
          ['Manejo Sanitário e Qualidade do Leite',      'SENAR', '2021'],
          ['Inseminação Artificial em Bovinos',           'SENAR', '2019'],
        ];
        foreach ($cursos as [$nome, $inst, $ano]):
        ?>
        <div class="curso-item">
          <div style="display:flex;align-items:center;gap:.5rem;">
            <div style="width:6px;height:6px;border-radius:50%;background:var(--verde-claro);flex-shrink:0;"></div>
            <strong style="font-size:.88rem;color:var(--preto);"><?= $nome ?></strong>
          </div>
          <div style="display:flex;gap:.5rem;align-items:center;margin-top:.25rem;padding-left:1rem;">
            <span style="font-size:.78rem;color:var(--cinza-medio);"><?= $inst ?></span>
            <span style="font-size:.72rem;background:var(--verde-suave);color:var(--verde-medio);
                         padding:.1rem .5rem;border-radius:20px;"><?= $ano ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- CTA FINAL -->
<section class="secao" style="background:var(--verde-escuro);">
  <div class="container" style="text-align:center;max-width:640px;">
    <div class="reveal">
      <span class="tag" style="background:rgba(82,183,136,.2);color:var(--verde-claro);">Vamos trabalhar juntos</span>
      <h2 style="color:#fff;font-size:clamp(1.6rem,3.5vw,2.4rem);margin:.75rem 0 1rem;">
        Pronto para transformar<br>sua propriedade?
      </h2>
      <p style="color:rgba(255,255,255,.7);margin-bottom:2rem;line-height:1.7;">
        Entre em contato para agendar uma visita técnica ou saiba mais sobre o programa gratuito do SENAR para produtores da região.
      </p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
        <a href="/contato?intencao=senar"   class="btn btn--branco">Parceria SENAR gratuita</a>
        <a href="/contato?intencao=clinica" class="btn btn--outline" style="border-color:rgba(255,255,255,.4);color:#fff;">
          Agendar visita clínica
        </a>
      </div>
    </div>
  </div>
</section>

<style>
.sobre-hero {
  display: grid; grid-template-columns: 1.1fr 0.9fr;
  gap: 4rem; align-items: start; padding-bottom: 3rem;
}
.sobre-hero__cargo {
  font-size: .9rem; color: var(--verde-medio); font-weight: 600;
  letter-spacing: .04em; margin: .25rem 0 1.25rem;
}
.sobre-hero__desc {
  font-size: 1rem; color: var(--cinza-escuro); line-height: 1.8;
  margin-bottom: .875rem;
}
.sobre-hero__foto { display: flex; flex-direction: column; gap: 1rem; }
.sobre-hero__img {
  width: 100%;
  aspect-ratio: 3/4;
  object-fit: cover;
  object-position: top center;
  border-radius: var(--raio-lg);
  display: block;
}
.sobre-hero__cards { display: grid; grid-template-columns: repeat(3,1fr); gap: .6rem; }
.mini-card {
  background: var(--cinza-claro); border-radius: var(--raio-sm);
  padding: .75rem; text-align: center;
}
.mini-card strong { display: block; font-size: .85rem; color: var(--verde-escuro); margin-bottom: .2rem; }
.mini-card span   { font-size: .72rem; color: var(--cinza-medio); line-height: 1.3; display: block; }

/* TIMELINE */
.timeline { position: relative; max-width: 740px; margin-inline: auto; }
.timeline::before {
  content: ''; position: absolute; left: 16px; top: 0; bottom: 0;
  width: 2px; background: var(--verde-suave);
}
.timeline-item {
  display: grid; grid-template-columns: 40px 1fr;
  gap: 1.25rem; margin-bottom: 2.5rem;
}
.timeline-dot {
  width: 14px; height: 14px; border-radius: 50%;
  background: var(--verde-escuro); border: 3px solid var(--verde-suave);
  margin-top: .4rem; margin-left: 3px; flex-shrink: 0; position: relative; z-index: 1;
}
.timeline-content { padding-bottom: .5rem; }
.timeline-periodo {
  font-size: .72rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .07em; color: var(--verde-medio);
}
.timeline-content h3 { font-size: 1rem; color: var(--preto); margin: .3rem 0 .2rem; }
.timeline-local { font-size: .82rem; color: var(--cinza-medio); margin-bottom: .5rem; }
.timeline-content p  { font-size: .88rem; color: var(--cinza-escuro); line-height: 1.6; }
.timeline-badge {
  display: inline-block; margin-top: .6rem; font-size: .72rem; font-weight: 600;
  background: var(--verde-suave); color: var(--verde-medio);
  padding: .2rem .75rem; border-radius: 20px;
}

/* FORMAÇÃO */
.formacao-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap: 1rem; }
.formacao-card {
  background: #fff; border: 1px solid rgba(0,0,0,.07);
  border-radius: var(--raio-md); padding: 1.25rem;
  display: flex; gap: 1rem; align-items: flex-start;
}
.formacao-icone {
  width: 42px; height: 42px; background: var(--verde-suave);
  border-radius: var(--raio-sm); display: flex; align-items: center;
  justify-content: center; flex-shrink: 0;
}
.formacao-icone svg { width: 20px; color: var(--verde-medio); }
.formacao-ano { font-size: .72rem; font-weight: 700; color: var(--verde-medio);
                text-transform: uppercase; letter-spacing: .05em; }
.formacao-card h3 { font-size: .95rem; color: var(--preto); margin: .25rem 0 .2rem; }
.formacao-card p  { font-size: .82rem; color: var(--cinza-medio); }

/* CURSOS */
.cursos-grid {
  display: grid; grid-template-columns: repeat(auto-fill,minmax(280px,1fr));
  gap: 1rem; max-width: 800px; margin-inline: auto;
}
.curso-item {
  background: #fff; border: 1px solid rgba(0,0,0,.07);
  border-radius: var(--raio-sm); padding: .875rem 1rem;
}

@media (max-width: 768px) {
  .sobre-hero { grid-template-columns: 1fr; gap: 2rem; }
  .sobre-hero__cards { grid-template-columns: repeat(3,1fr); }
  .timeline::before { left: 12px; }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>