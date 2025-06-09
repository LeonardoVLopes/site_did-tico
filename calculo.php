<?php
require_once 'config.php';

// Buscar questões de cálculo do banco de dados
$stmt = $pdo->prepare("SELECT q.id, q.enunciado, GROUP_CONCAT(DISTINCT CONCAT(a.letra, ':', a.texto) SEPARATOR '|') as alternativas 
                      FROM questoes q 
                      JOIN alternativas a ON q.id = a.questao_id 
                      WHERE q.categoria_id = 1 
                      GROUP BY q.id, q.enunciado");
$stmt->execute();
$questoes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado de Cálculo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">SimulaCiências</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="calculo.php">Cálculo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fisica.php">Física Ondulatória</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Simulado de Cálculo</h1>
        
        <div id="simulado-container">
            <?php foreach ($questoes as $index => $questao): ?>
                <div class="card mb-4 questao" id="questao-<?php echo $index + 1; ?>">
                    <div class="card-body">
                        <h5 class="card-title">Questão <?php echo $index + 1; ?></h5>
                        <p class="card-text"><?php echo $questao['enunciado']; ?></p>
                        
                        <?php if (strpos($questao['enunciado'], '[grafico]') !== false): ?>
                            <canvas class="grafico-questao mb-3" data-questao="<?php echo $index + 1; ?>"></canvas>
                        <?php endif; ?>

                        <div class="alternativas">
                            <?php 
                            $alternativas = explode('|', $questao['alternativas']);
                            foreach ($alternativas as $alternativa):
                                list($letra, $texto) = explode(':', $alternativa);
                            ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" 
                                           name="questao-<?php echo $index + 1; ?>" 
                                           id="<?php echo $index + 1; ?>-<?php echo $letra; ?>" 
                                           value="<?php echo $letra; ?>">
                                    <label class="form-check-label" for="<?php echo $index + 1; ?>-<?php echo $letra; ?>">
                                        <?php echo "$letra) $texto"; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="text-center mb-5">
                <button class="btn btn-primary btn-lg" onclick="verificarRespostas()">Verificar Respostas</button>
            </div>
        </div>
    </div>

    <script>
    // Função para criar gráficos das questões
    function criarGraficos() {
        const canvasElements = document.querySelectorAll('.grafico-questao');
        canvasElements.forEach(canvas => {
            const ctx = canvas.getContext('2d');
            const questaoId = canvas.dataset.questao;
            
            // Exemplo de gráfico para função quadrática
            const data = {
                labels: Array.from({length: 21}, (_, i) => i - 10),
                datasets: [{
                    label: 'f(x)',
                    data: Array.from({length: 21}, (_, i) => Math.pow(i - 10, 2)),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            };

            new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'x'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'y'
                            }
                        }
                    }
                }
            });
        });
    }

    // Função para verificar respostas
    function verificarRespostas() {
        const respostas = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
            const questaoId = input.name.split('-')[1];
            respostas[questaoId] = input.value;
        });

        fetch('verificar_respostas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(respostas)
        })
        .then(response => response.json())
        .then(data => {
            // Mostrar feedback para cada questão
            Object.entries(data).forEach(([questaoId, resultado]) => {
                const questaoElement = document.querySelector(`#questao-${questaoId}`);
                const feedback = document.createElement('div');
                feedback.className = `alert alert-${resultado.correto ? 'success' : 'danger'} mt-3`;
                feedback.innerHTML = resultado.correto ? 
                    'Correto! ' + resultado.explicacao :
                    'Incorreto. ' + resultado.explicacao;
                questaoElement.querySelector('.card-body').appendChild(feedback);
            });
        });
    }

    // Inicializar gráficos quando a página carregar
    document.addEventListener('DOMContentLoaded', criarGraficos);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>