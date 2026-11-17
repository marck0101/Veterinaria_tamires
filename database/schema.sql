-- ============================================================================
-- Banco do site Tamires Müller Veterinária (MySQL 5.7+ / MariaDB 10.3+)
--
-- Reconstruído em 17/11/2026 a partir das consultas do código (admin/, api/,
-- config/functions.php, pages/), porque o banco original ficava só na
-- InfinityFree e não tinha cópia no repositório.
--
-- Como usar: phpMyAdmin > banco > Importar > este arquivo.
-- Depois crie o primeiro admin (README.md, seção "Primeiro acesso ao painel").
-- ============================================================================

SET NAMES utf8mb4;

-- Login do painel /admin (senha guardada com password_hash do PHP)
CREATE TABLE IF NOT EXISTS admins (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome       VARCHAR(120) NOT NULL,
  email      VARCHAR(190) NOT NULL,
  senha      VARCHAR(255) NOT NULL,
  criado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Posts do blog. categoria: manejo | gestao | saude | senar
CREATE TABLE IF NOT EXISTS posts (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titulo        VARCHAR(255) NOT NULL,
  slug          VARCHAR(255) NOT NULL,
  resumo        TEXT         NULL,
  conteudo      MEDIUMTEXT   NOT NULL,
  categoria     VARCHAR(30)  NOT NULL DEFAULT 'manejo',
  imagem        VARCHAR(255) NULL,
  publicado     TINYINT(1)   NOT NULL DEFAULT 0,
  publicado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_posts_slug (slug),
  KEY ix_posts_publicado (publicado, publicado_em),
  KEY ix_posts_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pedidos de visita/contato (formulário /contato -> api/contato.php)
-- intencao: clinica | consultoria | procedimento | senar | outro
CREATE TABLE IF NOT EXISTS contatos (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome          VARCHAR(150) NOT NULL,
  telefone      VARCHAR(40)  NOT NULL,
  tipo_criacao  VARCHAR(255) NOT NULL,
  qtd_animais   VARCHAR(60)  NULL,
  intencao      VARCHAR(30)  NOT NULL DEFAULT 'outro',
  mensagem      TEXT         NULL,
  lido          TINYINT(1)   NOT NULL DEFAULT 0,
  criado_em     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_contatos_lido (lido, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inscritos na newsletter. O e-mail é único: api/newsletter.php trata o erro
-- 23000 como "já cadastrado". token = bin2hex(random_bytes(32)) (64 caracteres)
CREATE TABLE IF NOT EXISTS newsletter (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email            VARCHAR(190) NOT NULL,
  token            CHAR(64)     NOT NULL,
  ativo            TINYINT(1)   NOT NULL DEFAULT 1,
  criado_em        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  descadastrado_em DATETIME     NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_newsletter_email (email),
  UNIQUE KEY uq_newsletter_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cards de serviço da página inicial. icone: stethoscope | chart | syringe | scissors | handshake
CREATE TABLE IF NOT EXISTS servicos (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome      VARCHAR(120) NOT NULL,
  descricao TEXT         NOT NULL,
  icone     VARCHAR(30)  NOT NULL DEFAULT 'stethoscope',
  ativo     TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Textos dos serviços: os mesmos do tema (repositório Veterinaria_tamires_wordpress)
INSERT INTO servicos (nome, descricao, icone) VALUES
  ('Clínica Médica Bovina', 'Diagnóstico e tratamento de doenças em bovinos leiteiros com visita à propriedade.', 'stethoscope'),
  ('Consultoria Gerencial', 'Acompanhamento mensal com diagnóstico, análise de custos e plano de melhoria via SENAR.', 'chart'),
  ('Sanidade de Rebanho', 'Vacinação e controle sanitário para manter a produtividade do rebanho.', 'syringe'),
  ('Procedimentos Cirúrgicos', 'Mochamento e demais procedimentos cirúrgicos de rotina na bovinocultura leiteira.', 'scissors');
