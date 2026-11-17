# Tamires Müller · Veterinária

Site institucional + blog + newsletter da médica veterinária Tamires Müller (bovinos leiteiros, São Martinho - RS).
PHP 8 + MySQL, sem framework. Painel em `/admin` para posts, contatos e newsletter.

**Endereço:** https://veterinariatamires.lovestoblog.com (InfinityFree)

## Estrutura

```
index.php, router.php     página inicial e roteamento (regras no .htaccess)
pages/                    blog, post, serviços, sobre, contato, privacidade, descadastrar
admin/                    painel (login, posts, contatos, newsletter)
api/                      contato.php e newsletter.php (formulários)
config/
  env.php                 lê o .env
  conexao.php             cria o $pdo com as credenciais do .env
  functions.php           consultas e utilitários
  mailer.php, seo.php
database/
  schema.sql              estrutura do banco (+ textos dos serviços)
  gerar-admin.php         gera o SQL do primeiro usuário do painel
assets/img/posts/         imagens dos posts (enviadas pelo painel)
.env                      credenciais (NUNCA vai para o Git; ver .env.example)
```

## Colocar o site no ar (InfinityFree ou qualquer hospedagem PHP + MySQL)

1. **Hospedagem:** crie (ou reative) a conta e ligue o domínio/subdomínio a ela.
2. **Banco:** crie um banco MySQL no painel. Anote host, nome, usuário e senha.
3. **Estrutura:** phpMyAdmin → banco → *Importar* → `database/schema.sql`.
4. **Arquivos:** envie por FTP tudo para `htdocs/` (inclusive `vendor/` e `.htaccess`), **menos** `database/` e `.git/`.
5. **`.env`:** copie `.env.example` para `.env`, preencha e envie junto, na raiz de `htdocs/`:
   ```
   APP_ENV=production
   DB_PROD_HOST=...      (na InfinityFree: sqlXXX.infinityfree.com)
   DB_PROD_NAME=...
   DB_PROD_USER=...
   DB_PROD_PASS=...
   SITE_URL=https://veterinariatamires.lovestoblog.com
   MAIL_PASS=...         (senha de app do Gmail, para a newsletter)
   ```
6. **Primeiro acesso ao painel** (no seu computador, com PHP instalado):
   ```bash
   php database/gerar-admin.php "Tamires Müller" email@dominio.com "uma senha forte"
   ```
   Cole o `INSERT` que aparece na aba *SQL* do phpMyAdmin. Entre em `/admin/login.php`.

Se o banco não conectar, o site mostra "Site em manutenção" e o motivo vai para o log de erros do servidor.

## Backup (o que faltou quando a hospedagem caiu)

O código está no Git, mas **posts, contatos e inscritos ficam só no banco**. Uma vez por mês, e antes de qualquer mudança grande:

- phpMyAdmin → banco → *Exportar* → *Rápido* → SQL. Guarde fora da hospedagem (Google Drive, por exemplo).
- Baixe por FTP a pasta `assets/img/posts/` se houve post novo.

Backups com dados reais não vão para o Git (`*.sql` está no `.gitignore`, exceto `database/schema.sql`).

## InfinityFree: cuidados

- Conta gratuita **é desativada por falta de uso** (e pode ser excluída depois). Entre no painel de vez em quando.
- Ela coloca uma verificação em JavaScript na frente do site, que atrapalha o Google e os assistentes de IA. Para um site de cliente, uma hospedagem paga simples (com domínio próprio) é mais segura para SEO e para não sair do ar.
