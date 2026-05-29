<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

// Arquivos estáticos
$arquivo_fisico = ltrim($uri, '/');
if ($arquivo_fisico && file_exists($arquivo_fisico) && !is_dir($arquivo_fisico)) {
    return false;
}

// Rotas públicas
$rotas = [
    '/'         => 'index.php',
    '/sobre'    => 'pages/sobre.php',
    '/servicos' => 'pages/servicos.php',
    '/blog'     => 'pages/blog.php',
    '/contato'  => 'pages/contato.php',
    '/descadastrar'       => 'pages/descadastrar.php',
    '/politica-privacidade' => 'pages/politica-privacidade.php',
];

if (isset($rotas[$uri])) {
    require $rotas[$uri];
    return true;
}

// Post individual
if (preg_match('/^\/post\/([a-z0-9-]+)$/', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require 'pages/post.php';
    return true;
}

// API — resolve e executa com caminho absoluto
if (preg_match('/^\/api\/(.+\.php)$/', $uri, $m)) {
    $arquivo = __DIR__ . '/api/' . $m[1];
    if (file_exists($arquivo)) {
        require $arquivo;
        return true;
    }
    http_response_code(404);
    echo json_encode(['erro' => 'Endpoint não encontrado']);
    return true;
}

// Admin
if (preg_match('/^\/admin/', $uri)) {
    $base = __DIR__ . '/admin';
    $path = substr($uri, strlen('/admin')) ?: '/index.php';
    if (!pathinfo($path, PATHINFO_EXTENSION)) $path .= '.php';
    $arquivo = $base . $path;
    if (file_exists($arquivo)) {
        chdir($base);
        require $arquivo;
        return true;
    }
    http_response_code(404);
    echo '<h1>Página não encontrada</h1>';
    return true;
}

// 404
http_response_code(404);
echo '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:3rem;text-align:center;">
<h1 style="color:#1B4332;">404</h1><p>Página não encontrada.</p>
<a href="/" style="color:#2D6A4F;">← Voltar ao início</a>
</body></html>';