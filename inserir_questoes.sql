-- Inserir questões de Cálculo
INSERT INTO questoes (categoria_id, enunciado, dificuldade, resposta_correta, explicacao) VALUES
(1, 'Calcule a derivada da função f(x) = x² + 3x + 2. [grafico]', 'facil', 'A', 'A derivada de uma função quadrática segue a regra: se f(x) = ax² + bx + c, então f\'(x) = 2ax + b'),
(1, 'Determine a integral indefinida de f(x) = 2x + 1. [grafico]', 'medio', 'B', 'A integral indefinida adiciona uma constante C e aumenta o expoente em 1, dividindo pelo novo expoente'),
(1, 'Calcule o limite quando x tende a infinito de (x² + 2x) / (x² + 1). [grafico]', 'dificil', 'C', 'Para limites no infinito de funções racionais, compare os graus dos polinômios no numerador e denominador');

-- Inserir alternativas para questões de Cálculo
INSERT INTO alternativas (questao_id, texto, letra) VALUES
(1, '2x + 3', 'A'),
(1, '2x + 2', 'B'),
(1, 'x² + 3', 'C'),
(1, '2x', 'D'),
(2, 'x² + x', 'A'),
(2, 'x² + x + C', 'B'),
(2, '2x² + x', 'C'),
(2, '2x² + 1', 'D'),
(3, '0', 'A'),
(3, '2', 'B'),
(3, '1', 'C'),
(3, 'infinito', 'D');

-- Inserir questões de Física Ondulatória
INSERT INTO questoes (categoria_id, enunciado, dificuldade, resposta_correta, explicacao) VALUES
(2, 'Uma onda se propaga com frequência de 20 Hz. Qual é seu período? [onda]', 'facil', 'B', 'O período é o inverso da frequência: T = 1/f'),
(2, 'Analise o fenômeno de interferência entre duas ondas em fase. [onda]', 'medio', 'A', 'Na interferência construtiva, as ondas em fase se somam, aumentando a amplitude'),
(2, 'Explique o efeito Doppler observado na imagem. [onda]', 'dificil', 'D', 'O efeito Doppler ocorre quando há movimento relativo entre a fonte e o observador, alterando a frequência percebida');

-- Inserir alternativas para questões de Física Ondulatória
INSERT INTO alternativas (questao_id, texto, letra) VALUES
(4, '20 segundos', 'A'),
(4, '0,05 segundos', 'B'),
(4, '0,5 segundos', 'C'),
(4, '2 segundos', 'D'),
(5, 'A amplitude da onda resultante aumenta', 'A'),
(5, 'A amplitude da onda resultante diminui', 'B'),
(5, 'As ondas se cancelam completamente', 'C'),
(5, 'Não há alteração na amplitude', 'D'),
(6, 'A frequência permanece constante', 'A'),
(6, 'A amplitude aumenta', 'B'),
(6, 'O comprimento de onda não muda', 'C'),
(6, 'A frequência observada muda com o movimento relativo', 'D');