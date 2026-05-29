<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['erro' => 'Método não permitido']));
}

require_once __DIR__ . '/../config/conexao.php';

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

if (!$email) {
    http_response_code(400);
    die(json_encode(['erro' => 'E-mail inválido']));
}

// Gera token único para descadastro
$token = bin2hex(random_bytes(32));

try {
    $stmt = $pdo->prepare(
        "INSERT INTO newsletter (email, token) VALUES (?, ?)"
    );
    $stmt->execute([$email, $token]);
    echo json_encode([
        'sucesso'  => true,
        'mensagem' => 'Cadastro realizado com sucesso!'
    ]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode([
            'sucesso'  => false,
            'mensagem' => 'Este e-mail já está cadastrado.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao cadastrar: ' . $e->getMessage()]);
    }
}