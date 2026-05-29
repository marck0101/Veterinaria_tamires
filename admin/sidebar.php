<?php
$pg = basename($_SERVER['PHP_SELF'], '.php');
function sa(string $nome, string $atual): string {
  return $nome === $atual ? 'ativo' : '';
}
?>
<aside class="sidebar">
  <div class="sidebar__logo">
    <span>Tamires Müller</span>
    <small>Painel admin</small>
  </div>
  <nav class="sidebar__nav">
    <a href="/admin/" class="nav-item <?= sa('index', $pg) ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
      </svg>
      Dashboard
    </a>
    <a href="/admin/posts.php" class="nav-item <?= sa('posts', $pg) ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
        <path d="M17.586 3.586a2 2 0 112.828 2.828L12 15l-4 1 1-4 9.586-9.414z"/>
      </svg>
      Posts do blog
    </a>
    <a href="/admin/contatos.php" class="nav-item <?= sa('contatos', $pg) ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
      </svg>
      Contatos / Agendamentos
    </a>
    <a href="/admin/newsletter.php" class="nav-item <?= sa('newsletter', $pg) ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
      </svg>
      Newsletter
    </a>
    <a href="/" target="_blank" class="nav-item" style="margin-top:auto;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
      </svg>
      Ver site
    </a>
    <a href="/admin/newsletter-enviar.php"
   class="nav-item <?= sa('newsletter-enviar', $pg) ?>">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
  </svg>
  Enviar newsletter
</a>
  </nav>
  <div class="sidebar__footer">v1.0 — <?= date('Y') ?></div>
</aside>