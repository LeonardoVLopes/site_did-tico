<?php
require_once 'config.php';

// Buscar questões de física ondulatória do banco de dados
$stmt = $pdo->prepare("SELECT q.*, GROUP_CONCAT(CONCAT(a.letra, ':', a.texto) SEPARATOR '|') as alternativas 
                      FROM questoes q 
                      JOIN alternativas a ON q.id = a.questao_id 
                      WHERE q.categoria_id = 2 
                      GROUP BY q.id");
$stmt->execute();
$questoes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado de Física Ondulatória</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        .wave-canvas {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 10px 0;
        }
        .wave-controls {
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .wave-controls label {
            margin-right: 15px;
        }
    </style>
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
                        <a class="nav-link" href="calculo.php">Cálculo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="fisica.php">Física Ondulatória</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Simulado de Física Ondulatória</h1>
        
        <div id="simulado-container">
            <?php foreach ($questoes as $index => $questao): ?>
                <div class="card mb-4 questao" id="questao-<?php echo $index + 1; ?>">
                    <div class="card-body">
                        <h5 class="card-title">Questão <?php echo $index + 1; ?></h5>
                        <p class="card-text"><?php echo $questao['enunciado']; ?></p>
                        
                        <?php if (strpos($questao['enunciado'], '[onda]') !== false): ?>
                            <div class="wave-simulation">
                                <canvas class="wave-canvas" width="600" height="300" 
                                        data-questao="<?php echo $index + 1; ?>"></canvas>
                                <div class="wave-controls">
                                    <label>
                                        Amplitude:
                                        <input type="range" class="amplitude-control" min="10" max="100" value="50">
                                    </label>
                                    <label>
                                        Frequência:
                                        <input type="range" class="frequency-control" min="1" max="10" value="5">
                                    </label>
                                    <label>
                                        Velocidade:
                                        <input type="range" class="speed-control" min="1" max="10" value="5">
                                    </label>
                                </div>
                            </div>
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
    class WaveSimulation {
        constructor(canvas, controls) {
            this.canvas = canvas;
            this.ctx = canvas.getContext('2d');
            this.width = canvas.width;
            this.height = canvas.height;
            this.centerY = this.height / 2;
            this.controls = controls;
            this.time = 0;
            
            this.amplitude = 50;
            this.frequency = 5;
            this.speed = 5;
            
            this.setupControls();
            this.animate();
        }

        setupControls() {
            const amplitudeControl = this.controls.querySelector('.amplitude-control');
            const frequencyControl = this.controls.querySelector('.frequency-control');
            const speedControl = this.controls.querySelector('.speed-control');

            amplitudeControl.addEventListener('input', (e) => {
                this.amplitude = parseInt(e.target.value);
            });

            frequencyControl.addEventListener('input', (e) => {
                this.frequency = parseInt(e.target.value);
            });

            speedControl.addEventListener('input', (e) => {
                this.speed = parseInt(e.target.value);
            });
        }

        drawWave() {
            this.ctx.clearRect(0, 0, this.width, this.height);
            this.ctx.beginPath();
            this.ctx.moveTo(0, this.centerY);

            for (let x = 0; x < this.width; x++) {
                const y = this.centerY + 
                          this.amplitude * 
                          Math.sin((x * this.frequency * 0.02) + 
                          (this.time * this.speed * 0.05));
                this.ctx.lineTo(x, y);
            }

            this.ctx.strokeStyle = '#0d6efd';
            this.ctx.lineWidth = 2;
            this.ctx.stroke();
        }

        animate() {
            this.time++;
            this.drawWave();
            requestAnimationFrame(() => this.animate());
        }
    }

    // Inicializar simulações de onda
    function inicializarSimulacoes() {
        document.querySelectorAll('.wave-simulation').forEach(simulation => {
            const canvas = simulation.querySelector('.wave-canvas');
            const controls = simulation.querySelector('.wave-controls');
            new WaveSimulation(canvas, controls);
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

    // Inicializar simulações quando a página carregar
    document.addEventListener('DOMContentLoaded', inicializarSimulacoes);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>