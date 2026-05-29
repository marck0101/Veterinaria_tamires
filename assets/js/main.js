// Hamburger menu
const hamburger = document.getElementById('hamburger');
const navLista  = document.getElementById('nav-lista');
hamburger?.addEventListener('click', () => navLista.classList.toggle('aberto'));

// Reveal on scroll
const observer = new IntersectionObserver(
  (entries) => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visivel'); }),
  { threshold: 0.12 }
);
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Formulário de contato
const formContato = document.getElementById('form-contato');
formContato?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn    = formContato.querySelector('[type=submit]');
  const aviso  = document.getElementById('form-aviso');
  btn.disabled = true;
  btn.textContent = 'Enviando…';
  try {
    const res  = await fetch('/api/contato.php', { method: 'POST', body: new FormData(formContato) });
    const data = await res.json();
    aviso.className = 'form__aviso ' + (data.sucesso ? 'sucesso' : 'erro');
    aviso.textContent = data.mensagem ?? data.erro;
    if (data.sucesso) formContato.reset();
  } catch {
    aviso.className = 'form__aviso erro';
    aviso.textContent = 'Erro ao enviar. Tente novamente.';
  }
  btn.disabled = false;
  btn.textContent = 'Enviar solicitação';
});

// Newsletter
const formNews = document.getElementById('form-newsletter');
formNews?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn   = formNews.querySelector('[type=submit]');
  const aviso = document.getElementById('news-aviso');
  btn.disabled = true;
  btn.textContent = '…';
  const res  = await fetch('/api/newsletter.php', { method: 'POST', body: new FormData(formNews) });
  const data = await res.json();
  aviso.className = 'form__aviso ' + (data.sucesso ? 'sucesso' : 'erro');
  aviso.textContent = data.mensagem ?? data.erro;
  if (data.sucesso) formNews.reset();
  btn.disabled = false;
  btn.textContent = 'Cadastrar';
});