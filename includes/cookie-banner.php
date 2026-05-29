<?php if (!isset($_COOKIE['cookie_consent'])): ?>
<div id="cookie-banner" role="dialog" aria-live="polite" aria-label="Aviso de cookies">
  <p>
    Utilizamos cookies para melhorar sua experiência, analisar o tráfego e personalizar conteúdo,
    conforme nossa <a href="/politica-privacidade">Política de Privacidade</a>.
    Ao continuar navegando, você concorda com o uso de cookies essenciais.
  </p>
  <div class="cookie-banner__acoes">
    <button id="cookie-aceitar" type="button">Aceitar todos</button>
    <button id="cookie-recusar" type="button">Recusar não essenciais</button>
  </div>
</div>

<style>
#cookie-banner {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #1a1a2e;
  color: #f0f0f0;
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  z-index: 9999;
  font-size: 0.9rem;
  box-shadow: 0 -2px 8px rgba(0,0,0,.3);
}
#cookie-banner a { color: #7eb8f7; }
.cookie-banner__acoes { display: flex; gap: .75rem; flex-shrink: 0; }
.cookie-banner__acoes button {
  padding: .5rem 1.2rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: .875rem;
  font-weight: 600;
}
#cookie-aceitar  { background: #28a745; color: #fff; }
#cookie-recusar  { background: transparent; color: #ccc; border: 1px solid #666; }
</style>

<script>
(function () {
  var banner = document.getElementById('cookie-banner');
  var EXPIRY_DAYS = 365;

  function setCookie(name, value, days) {
    var d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
  }

  function hideBanner() {
    banner.style.display = 'none';
  }

  document.getElementById('cookie-aceitar').addEventListener('click', function () {
    setCookie('cookie_consent', 'accepted', EXPIRY_DAYS);
    hideBanner();
  });

  document.getElementById('cookie-recusar').addEventListener('click', function () {
    setCookie('cookie_consent', 'rejected', EXPIRY_DAYS);
    hideBanner();
  });
})();
</script>
<?php endif; ?>
