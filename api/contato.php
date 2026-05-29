<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['erro' => 'Método não permitido']));
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$nome        = sanitize($_POST['nome']        ?? '');
$telefone    = sanitize($_POST['telefone']    ?? '');
$municipio   = sanitize($_POST['municipio']   ?? '');
$distancia   = sanitize($_POST['distancia']   ?? '');
$tipo_criacao= sanitize($_POST['tipo_criacao']?? '');
$qtd_animais = sanitize($_POST['qtd_animais'] ?? '');
$animal_desc = sanitize($_POST['animal_desc'] ?? '');
$intencao    = sanitize($_POST['intencao']    ?? 'outro');
$mensagem    = sanitize($_POST['mensagem']    ?? '');

if (!$nome || !$telefone || !$tipo_criacao) {
    http_response_code(400);
    die(json_encode(['erro' => 'Nome, telefone e tipo de criação são obrigatórios.']));
}

$validas = ['clinica','consultoria','procedimento','senar','outro'];
if (!in_array($intencao, $validas)) $intencao = 'outro';

$obs = trim("$animal_desc\n$mensagem");

$stmt = $pdo->prepare("
    INSERT INTO contatos
      (nome, telefone, tipo_criacao, qtd_animais, intencao, mensagem)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $nome,
    $telefone,
    "$tipo_criacao — $municipio ($distancia)",
    $qtd_animais,
    $intencao,
    $obs
]);

echo json_encode([
    'sucesso'  => true,
    'mensagem' => 'Solicitação enviada! Tamires entrará em contato em até 24h.'
]);