<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$token = sanitize($_GET['token'] ?? '');
$email = sanitize($_GET['email'] ?? '');
$acao  = $_POST['acao'] ?? '';

$assinante = null;
if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM newsletter WHERE token = ? AND ativo = 1");
    $stmt->execute([$token]);
    $assinante = $stmt->fetch();
}

$descadastrado = false;
if ($acao === 'confirmar' && $assinante) {
    $pdo->prepare("UPDATE newsletter SET ativo = 0, descadastrado_em = NOW() WHERE token = ?")
        ->execute([$token]);
    $descadastrado = true;
    $assinante = null;
}

$page_title = 'Cancelar inscrição — Tamires Müller Veterinária';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="secao">
  <div class="container" style="max-width:540px;">

    <?php if ($descadastrado): ?>
      <!-- CONFIRMAÇÃO DE DESCADASTRO -->
      <div style="text-align:center;padding:2rem 0;">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--verde-suave);
                    display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--verde-medio)"
               stroke-width="2.5" style="width:28px;">
            <path d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <h1 style="font-size:1.6rem;color:var(--verde-escuro);margin-bottom:.75rem;">
          Descadastro realizado
        </h1>
        <p style="color:var(--cinza-medio);margin-bottom:.5rem;">
          O e-mail <strong><?= htmlspecialchars($email) ?></strong>
          foi removido da lista de newsletter.
        </p>
        <p style="font-size:.88rem;color:var(--cinza-medio);margin-bottom:2rem;">
          Você não receberá mais comunicações nossas.<br>
          Sentiremos sua falta!
        </p>
        <div style="background:var(--cinza-claro);border-radius:var(--raio-md);
                    padding:1.25rem;text-align:left;margin-bottom:2rem;">
          <p style="font-size:.85rem;color:var(--cinza-escuro);margin:0;">
            Quer continuar acompanhando conteúdos sobre manejo animal e gestão rural?
            Acesse o blog quando quiser — sem precisar se cadastrar.
          </p>
        </div>
        <a href="/blog" class="btn btn--primario">Ver artigos do blog</a>
        <a href="/" class="btn btn--outline" style="margin-left:.75rem;">Ir para o início</a>
      </div>

    <?php elseif ($assinante): ?>
      <!-- CONFIRMAÇÃO ANTES DE DESCADASTRAR -->
      <div style="text-align:center;padding:2rem 0;">
        <div style="width:64px;height:64px;border-radius:50%;background:#FEF3C7;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#92400E"
               stroke-width="2" style="width:28px;">
            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h1 style="font-size:1.5rem;color:var(--preto);margin-bottom:.75rem;">
          Cancelar inscrição na newsletter?
        </h1>
        <p style="color:var(--cinza-medio);margin-bottom:.5rem;">
          O e-mail <strong><?= htmlspecialchars($assinante['email']) ?></strong>
          será removido da lista.
        </p>
        <p style="font-size:.85rem;color:var(--cinza-medio);margin-bottom:2rem;">
          Você não receberá mais nossas dicas de manejo animal e gestão rural.
          Esta ação pode ser revertida cadastrando-se novamente.
        </p>

        <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;">
          <form method="POST">
            <input type="hidden" name="acao" value="confirmar">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <button type="submit" class="btn btn--outline"
                    style="border-color:rgba(220,38,38,.4);color:#991B1B;">
              Sim, quero sair da lista
            </button>
          </form>
          <a href="/" class="btn btn--primario">
            Manter minha inscrição
          </a>
        </div>

        <p style="font-size:.75rem;color:var(--cinza-medio);margin-top:2rem;">
          Em conformidade com a
          <strong>Lei Geral de Proteção de Dados (LGPD — Lei 13.709/2018)</strong>.
          Seus dados são usados apenas para envio de conteúdo solicitado.
        </p>
      </div>

    <?php else: ?>
      <!-- TOKEN INVÁLIDO OU JÁ DESCADASTRADO -->
      <div style="text-align:center;padding:2rem 0;">
        <h1 style="font-size:1.5rem;color:var(--preto);margin-bottom:.75rem;">
          Link inválido ou já processado
        </h1>
        <p style="color:var(--cinza-medio);margin-bottom:1.5rem;">
          Este link de descadastro já foi utilizado ou expirou.<br>
          Se ainda receber e-mails, entre em contato.
        </p>
        <a href="/" class="btn btn--primario">Voltar ao início</a>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>