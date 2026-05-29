class FiltroMultiplo {
  constructor(opcoes) {
    this.container   = document.querySelector(opcoes.container);
    this.itens       = document.querySelectorAll(opcoes.itens);
    this.atributo    = opcoes.atributo || 'data-cat';
    this.paramURL    = opcoes.paramURL  || 'cat';
    this.selecionados = new Set();
    this.contador    = document.querySelector(opcoes.contador || '#filtro-contador');

    this._lerURL();
    this._renderizar();
    this._aplicar();
  }

  _lerURL() {
    const params = new URLSearchParams(window.location.search);
    const vals   = params.getAll(this.paramURL);
    vals.forEach(v => this.selecionados.add(v));
  }

  _salvarURL() {
    const params = new URLSearchParams();
    this.selecionados.forEach(v => params.append(this.paramURL, v));
    const nova = params.toString()
      ? window.location.pathname + '?' + params.toString()
      : window.location.pathname;
    history.replaceState(null, '', nova);
  }

  toggle(valor) {
    if (this.selecionados.has(valor)) {
      this.selecionados.delete(valor);
    } else {
      this.selecionados.add(valor);
    }
    this._renderizar();
    this._aplicar();
    this._salvarURL();
  }

  limpar() {
    this.selecionados.clear();
    this._renderizar();
    this._aplicar();
    this._salvarURL();
  }

  _renderizar() {
    if (!this.container) return;
    this.container.querySelectorAll('[data-filtro-val]').forEach(btn => {
      const val   = btn.dataset.filtroVal;
      const ativo = this.selecionados.has(val);
      btn.classList.toggle('ativo', ativo);
      btn.setAttribute('aria-pressed', ativo);
    });
    const btnLimpar = this.container.querySelector('[data-filtro-limpar]');
    if (btnLimpar) {
      btnLimpar.style.display = this.selecionados.size > 0 ? 'inline-flex' : 'none';
    }
    if (this.contador) {
      this.contador.textContent = this.selecionados.size > 0
        ? this.selecionados.size + ' filtro' + (this.selecionados.size > 1 ? 's' : '')
        : '';
      this.contador.style.display = this.selecionados.size > 0 ? 'inline-block' : 'none';
    }
  }

  _aplicar() {
    let visiveis = 0;
    this.itens.forEach(item => {
      const cats = (item.getAttribute(this.atributo) || '').split(' ');
      const mostrar = this.selecionados.size === 0
        || cats.some(c => this.selecionados.has(c));
      item.style.display  = mostrar ? '' : 'none';
      item.style.opacity  = mostrar ? '1' : '0';
      if (mostrar) visiveis++;
    });

    // Mensagem de vazio
    const vazio = document.querySelector('[data-filtro-vazio]');
    if (vazio) vazio.style.display = visiveis === 0 ? 'block' : 'none';
  }
}