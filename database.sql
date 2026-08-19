-- ============================================================
-- BANCO DE DADOS: PLATAFORMA DE SERVIÇOS PET
-- ============================================================
-- Autor: Sistema Automatizado
-- Descrição: Script completo para criação do banco de dados,
--            tabelas e dados iniciais (seeds).
-- 
-- INSTRUÇÕES DE USO:
-- 1. Acesse o phpMyAdmin ou terminal MySQL
-- 2. Crie o banco: CREATE DATABASE petservices;
-- 3. Execute este script completo
-- ============================================================

-- Criar banco de dados (descomente se necessário)
-- CREATE DATABASE IF NOT EXISTS petservices CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE petservices;

-- ============================================================
-- TABELA: servicos
-- Armazena todos os serviços oferecidos pela plataforma.
-- IMPORTANTE: Novos serviços são adicionados aqui, NÃO no código!
-- ============================================================
CREATE TABLE IF NOT EXISTS servicos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL COMMENT 'Nome do serviço (ex: Hotel Pet)',
    descricao TEXT NOT NULL COMMENT 'Descrição detalhada do serviço',
    preco_base DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Preço base em reais',
    ativo TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = ativo, 0 = inativo (não aparece no site)',
    icone_bootstrap VARCHAR(50) NOT NULL DEFAULT 'bi-star' COMMENT 'Classe do ícone Bootstrap (ex: bi-house)',
    tipo_calculo ENUM('diaria', 'unidade', 'hora') NOT NULL DEFAULT 'diaria' COMMENT 'Como calcular o orçamento',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: cotacoes
-- Armazena as solicitações de orçamento dos clientes.
-- ============================================================
CREATE TABLE IF NOT EXISTS cotacoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_cliente VARCHAR(150) NOT NULL COMMENT 'Nome do cliente',
    email VARCHAR(255) NOT NULL COMMENT 'Email para contato',
    telefone VARCHAR(20) DEFAULT NULL COMMENT 'Telefone (opcional)',
    servicos_selecionados TEXT NOT NULL COMMENT 'JSON com serviços e quantidades',
    quantidade_pets INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Número de pets',
    valor_estimado DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor calculado',
    desconto_aplicado DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Desconto se 2+ pets',
    valor_final DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor após desconto',
    mensagem TEXT DEFAULT NULL COMMENT 'Observações do cliente',
    status ENUM('pendente', 'respondida', 'convertida', 'cancelada') NOT NULL DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: administradores
-- Usuários com acesso ao painel administrativo.
-- ============================================================
CREATE TABLE IF NOT EXISTS administradores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL COMMENT 'Senha criptografada com password_hash()',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    ultimo_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEEDS: Dados Iniciais
-- ============================================================

-- Inserir 3 serviços iniciais
INSERT INTO servicos (nome, descricao, preco_base, ativo, icone_bootstrap, tipo_calculo) VALUES
(
    'Hospedagem (Hotel)',
    'Seu pet merece férias também! Hospedagem completa com acomodações confortáveis, alimentação balanceada, monitoramento 24h e muito carinho. Ideal para viagens ou compromissos.',
    120.00,
    1,
    'bi-house-heart',
    'diaria'
),
(
    'Dog Walking',
    'Passeios diários com condutores experientes. Exercício físico, socialização e diversão garantidos. Rotas seguras e acompanhamento via GPS. Seu cão vai adorar!',
    45.00,
    1,
    'bi-signpost-2',
    'unidade'
),
(
    'Pet Sitter',
    'Cuidador profissional vai até sua casa! Alimentação, brincadeiras, medicação se necessário e companhia. Perfeito para pets que preferem ficar em casa.',
    80.00,
    1,
    'bi-person-heart',
    'hora'
);

-- Inserir administrador padrão
-- IMPORTANTE: Altere a senha após o primeiro acesso!
-- Senha padrão: admin123 (já criptografada)
INSERT INTO administradores (nome, email, senha_hash, ativo) VALUES
(
    'Administrador',
    'admin@petservices.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    1
);

-- ============================================================
-- FIM DO SCRIPT
-- ============================================================
