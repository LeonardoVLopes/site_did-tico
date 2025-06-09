-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS simulados_db;

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT
);

-- Tabela de questões
CREATE TABLE IF NOT EXISTS questoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    enunciado TEXT NOT NULL,
    dificuldade ENUM('facil', 'medio', 'dificil') NOT NULL,
    resposta_correta VARCHAR(1) NOT NULL,
    explicacao TEXT,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabela de alternativas
CREATE TABLE IF NOT EXISTS alternativas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questao_id INT NOT NULL,
    texto TEXT NOT NULL,
    letra VARCHAR(1) NOT NULL,
    FOREIGN KEY (questao_id) REFERENCES questoes(id)
);

-- Tabela de progresso dos usuários
CREATE TABLE IF NOT EXISTS progresso_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questao_id INT NOT NULL,
    resposta_usuario VARCHAR(1),
    acertou BOOLEAN,
    data_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (questao_id) REFERENCES questoes(id)
);

-- Inserir categorias iniciais
INSERT INTO categorias (nome, descricao) VALUES
('Cálculo', 'Questões sobre derivadas, integrais, limites e outros conceitos de cálculo'),
('Física Ondulatória', 'Questões sobre ondas, interferência, difração e fenômenos ondulatórios');