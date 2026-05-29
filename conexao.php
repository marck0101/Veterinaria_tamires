<?php
$host = 'localhost';
$banco = 'clinica_veterinaria';
$usuario = 'root';
$senha = ''; // senha no MariaDB

try {
    // Cria a conexão
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    
    // Configura o PDO para mostrar erros caso algo dê errado
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Sucesso! O PHP conectou no banco de dados da Veterinária! 🐶🐱";

} catch (PDOException $e) {
    echo "Erro ao conectar: " . $e->getMessage();
}
?>