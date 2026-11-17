<?php
/**
 * Lê o arquivo .env da raiz do projeto (uma vez) e devolve variáveis.
 * Hospedagens como a InfinityFree não deixam configurar variáveis de
 * ambiente no painel: o .env enviado por FTP faz esse papel. O .htaccess
 * bloqueia o acesso a ele pelo navegador.
 */
function env(string $chave, ?string $padrao = null): ?string
{
    static $valores = null;
    if ($valores === null) {
        $valores = [];
        $arquivo = __DIR__ . '/../.env';
        if (is_readable($arquivo)) {
            foreach (file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
                $linha = trim($linha);
                if ($linha === '' || $linha[0] === '#' || strpos($linha, '=') === false) {
                    continue;
                }
                [$k, $v] = array_map('trim', explode('=', $linha, 2));
                $valores[$k] = trim($v, "\"'");
            }
        }
    }
    $doSistema = getenv($chave);
    if ($doSistema !== false && $doSistema !== '') {
        return $doSistema;
    }
    return $valores[$chave] ?? $padrao;
}
