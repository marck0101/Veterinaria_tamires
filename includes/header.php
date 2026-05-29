<?php
$pagina_atual = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../config/seo.php';
function nav_ativo(string $pagina, string $atual): string {
  return $pagina === $atual ? 'ativo' : '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php seo_tags(['titulo' => $page_title ?? null, 'desc' => $page_desc ?? null]); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="/assets/css/main.css">
</head>
<?php require_once __DIR__ . '/cookie-banner.php'; ?>