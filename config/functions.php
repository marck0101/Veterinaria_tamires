<?php

function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function slugify(string $texto): string {
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = preg_replace('/[áàãâä]/u', 'a', $texto);
    $texto = preg_replace('/[éèêë]/u',  'e', $texto);
    $texto = preg_replace('/[íìîï]/u',  'i', $texto);
    $texto = preg_replace('/[óòõôö]/u', 'o', $texto);
    $texto = preg_replace('/[úùûü]/u',  'u', $texto);
    $texto = preg_replace('/[ç]/u',     'c', $texto);
    $texto = preg_replace('/[^a-z0-9\s-]/', '', $texto);
    $texto = preg_replace('/[\s-]+/', '-', trim($texto));
    return $texto;
}

function slugUnico(PDO $pdo, string $titulo, int $ignorar_id = 0): string {
    $base     = slugify($titulo);
    $slug     = $base;
    $contador = 1;

    while (true) {
        $sql    = $ignorar_id
            ? "SELECT id FROM posts WHERE slug = ? AND id != ?"
            : "SELECT id FROM posts WHERE slug = ?";
        $params = $ignorar_id ? [$slug, $ignorar_id] : [$slug];
        $check  = $pdo->prepare($sql);
        $check->execute($params);
        if (!$check->fetch()) break;
        $slug = $base . '-' . $contador;
        $contador++;
    }

    return $slug;
}

function getServicos(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY id");
    return $stmt->fetchAll();
}

function getPosts(PDO $pdo, int $limit = 6, int $offset = 0, string $categoria = ''): array {
    if ($categoria) {
        $stmt = $pdo->prepare(
            "SELECT * FROM posts WHERE publicado = 1 AND categoria = ?
             ORDER BY publicado_em DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$categoria, $limit, $offset]);
    } else {
        $stmt = $pdo->prepare(
            "SELECT * FROM posts WHERE publicado = 1
             ORDER BY publicado_em DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$limit, $offset]);
    }
    return $stmt->fetchAll();
}

function getPostBySlug(PDO $pdo, string $slug): array|false {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ? AND publicado = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}