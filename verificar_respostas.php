<?php
require_once 'config.php';

// Receber dados do POST
$dados = json_decode(file_get_contents('php://input'), true);

$resultados = [];

foreach ($dados as $questaoId => $respostaUsuario) {
    // Buscar informações da questão
    $stmt = $pdo->prepare("SELECT q.*, c.nome as categoria 
                          FROM questoes q 
                          JOIN categorias c ON q.categoria_id = c.id 
                          WHERE q.id = ?");
    $stmt->execute([$questaoId]);
    $questao = $stmt->fetch();

    // Verificar se a resposta está correta
    $correto = $questao['resposta_correta'] === $respostaUsuario;

    // Registrar resposta no histórico
    $stmt = $pdo->prepare("INSERT INTO progresso_usuario (questao_id, resposta_usuario, acertou) 
                          VALUES (?, ?, ?)");
    $stmt->execute([$questaoId, $respostaUsuario, (int)$correto]); // Converter booleano para inteiro

    // Preparar feedback personalizado
    $resultados[$questaoId] = [
        'correto' => $correto,
        'explicacao' => $questao['explicacao']
    ];

    // Adicionar dicas específicas baseadas na categoria
    if (!$correto) {
        if ($questao['categoria'] === 'Cálculo') {
            $resultados[$questaoId]['dica'] = 'Revise os conceitos fundamentais e tente visualizar o problema graficamente.';
        } else if ($questao['categoria'] === 'Física Ondulatória') {
            $resultados[$questaoId]['dica'] = 'Observe atentamente o comportamento da onda na simulação e relacione com os conceitos teóricos.';
        }
    }
}

// Retornar resultados em formato JSON
header('Content-Type: application/json');
echo json_encode($resultados);