CREATE DATABASE IF NOT EXISTS concessionaria
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE concessionaria;

DROP TABLE IF EXISTS locacoes;
DROP TABLE IF EXISTS veiculos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;

-- ==========================================
-- USUÁRIOS (senha de todos: 123456)
-- ==========================================

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nome, email, senha) VALUES
('Administrador', 'admin@locadora.com', '$2y$10$abcdefghijklmnopqrstuuxD0vCwu/f2ahNVJLL35ZYA2xuiRyPH6'),
('João Silva', 'joao@locadora.com', '$2y$10$abcdefghijklmnopqrstuuxD0vCwu/f2ahNVJLL35ZYA2xuiRyPH6'),
('Maria Souza', 'maria@locadora.com', '$2y$10$abcdefghijklmnopqrstuuxD0vCwu/f2ahNVJLL35ZYA2xuiRyPH6');

-- ==========================================
-- CLIENTES (CPF armazenado criptografado)
-- ==========================================

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cpf VARCHAR(255) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO clientes (nome, cpf, email, telefone, endereco) VALUES
('Carlos Oliveira', 'CtRRGzF8AAhNggjvsa6Pi6kkzQgdUmXSLv3ogltNWow=', 'carlos@email.com', '81999990001', 'Rua A, 100'),
('Ana Santos', 'odCFtmBL7ShLox6XAXpIcInL1P/9+W/e/vjP7dpmUZo=', 'ana@email.com', '81999990002', 'Rua B, 200'),
('Pedro Almeida', '/EGdS0YHiafygMCnkpQtcNfYzuPtfJ0+EmI1Bt0/0ow=', 'pedro@email.com', '81999990003', 'Rua C, 300');

-- ==========================================
-- VEÍCULOS
-- ==========================================

CREATE TABLE veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) NOT NULL UNIQUE,
    modelo VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    ano INT NOT NULL,
    cor VARCHAR(50),
    valor_diaria DECIMAL(10,2) NOT NULL,
    status ENUM('Disponível', 'Alugado', 'Manutenção') NOT NULL DEFAULT 'Disponível',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO veiculos (placa, modelo, marca, ano, cor, valor_diaria, status) VALUES
('ABC1D23', 'Onix', 'Chevrolet', 2023, 'Branco', 120.00, 'Alugado'),
('DEF4G56', 'HB20', 'Hyundai', 2022, 'Prata', 110.00, 'Alugado'),
('GHI7J89', 'Corolla', 'Toyota', 2024, 'Preto', 180.00, 'Alugado'),
('JKL0M12', 'Argo', 'Fiat', 2023, 'Vermelho', 100.00, 'Disponível'),
('NOP3Q45', 'Polo', 'Volkswagen', 2024, 'Cinza', 140.00, 'Disponível');

-- ==========================================
-- LOCAÇÕES
-- ==========================================

CREATE TABLE locacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_veiculo INT NOT NULL,
    data_retirada DATE NOT NULL,
    data_devolucao DATE NOT NULL,
    valor_diaria DECIMAL(10,2) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    status ENUM('Aberta', 'Finalizada', 'Cancelada') NOT NULL DEFAULT 'Aberta',

    CONSTRAINT fk_locacao_cliente
        FOREIGN KEY (id_cliente) REFERENCES clientes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_locacao_veiculo
        FOREIGN KEY (id_veiculo) REFERENCES veiculos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

INSERT INTO locacoes
(id_cliente, id_veiculo, data_retirada, data_devolucao, valor_diaria, valor_total, status)
VALUES
(1, 1, '2026-10-01', '2026-10-05', 120.00, 480.00, 'Aberta'),
(2, 2, '2026-10-03', '2026-10-06', 110.00, 330.00, 'Aberta'),
(3, 3, '2026-10-05', '2026-10-10', 180.00, 900.00, 'Aberta');
