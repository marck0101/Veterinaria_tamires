<?php
/**
 * Conexão com o banco ($pdo). As credenciais vêm do .env (nunca deste arquivo),
 * então este arquivo pode ficar no Git. Antes ele era ignorado pelo Git e só
 * existia no servidor: quando a hospedagem caiu, ele se perdeu junto.
 *
 * APP_ENV=production usa DB_PROD_*; qualquer outro valor usa DB_LOCAL_*.
 */
require_once __DIR__ . '/env.php';

$prefixo = env('APP_ENV', 'local') === 'production' ? 'DB_PROD_' : 'DB_LOCAL_';

$host  = env($prefixo . 'HOST', 'localhost');
$porta = env($prefixo . 'PORT', '3306');
$banco = env($prefixo . 'NAME', '');
$user  = env($prefixo . 'USER', '');
$senha = env($prefixo . 'PASS', '');

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4",
        $user,
        $senha,
        // Mesmo modo do código original; o resto fica no padrão do PDO
        // (as consultas do site contam com esse comportamento)
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    // Detalhe técnico vai para o log do servidor, nunca para o visitante
    error_log('Falha na conexão com o banco: ' . $e->getMessage());
    http_response_code(503);
    exit('Site em manutenção. Tente novamente em alguns minutos.');
}
