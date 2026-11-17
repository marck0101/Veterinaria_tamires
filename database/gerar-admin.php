<?php
/**
 * Gera o SQL do primeiro usuário do painel /admin, com a senha já criptografada
 * (password_hash). Rode no seu computador, cole o resultado no phpMyAdmin.
 *
 *   php database/gerar-admin.php "Tamires Müller" email@exemplo.com "senha-forte"
 *
 * Não envie este arquivo para o servidor (não é necessário lá).
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

[$_, $nome, $email, $senha] = array_pad($argv, 4, null);

if (!$nome || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $senha) < 10) {
    fwrite(STDERR, "Uso: php database/gerar-admin.php \"Nome\" email@dominio.com \"senha com 10+ caracteres\"\n");
    exit(1);
}

$q = fn(string $v) => "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $v) . "'";
$hash = password_hash($senha, PASSWORD_DEFAULT);

echo "INSERT INTO admins (nome, email, senha) VALUES ({$q($nome)}, {$q($email)}, {$q($hash)});\n";
