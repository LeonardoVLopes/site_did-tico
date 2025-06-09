<?php
$host = 'localhost';
$dbname = 'simulados_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Função para limpar e validar entrada de dados
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Função para verificar se a resposta está correta
function verificarResposta($questao_id, $resposta_usuario) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT resposta_correta FROM questoes WHERE id = ?");
    $stmt->execute([$questao_id]);
    $questao = $stmt->fetch();
    
    return $questao['resposta_correta'] === $resposta_usuario;
}