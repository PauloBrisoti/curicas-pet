-- ============================================================
-- ATUALIZAÇÃO: TABELA DE PETS PARA ADOÇÃO
-- ============================================================
-- Execute este script no mesmo banco de dados
-- ============================================================

-- Criar tabela de pets para adoção
CREATE TABLE IF NOT EXISTS pets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL COMMENT 'Nome do pet',
    idade VARCHAR(50) NOT NULL COMMENT 'Idade aproximada (ex: 2 anos, 6 meses)',
    porte ENUM('Pequeno', 'Médio', 'Grande') NOT NULL DEFAULT 'Médio' COMMENT 'Porte do animal',
    sexo ENUM('M', 'F') NOT NULL COMMENT 'Sexo: M = Macho, F = Fêmea',
    raca VARCHAR(100) NOT NULL DEFAULT 'SRD' COMMENT 'Raça ou SRD (Sem Raça Definida)',
    descricao TEXT NOT NULL COMMENT 'Descrição do pet e personalidade',
    imagem VARCHAR(255) DEFAULT NULL COMMENT 'Caminho da imagem do pet',
    vacinado TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = vacinado, 0 = não vacinado',
    castrado TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = castrado, 0 = não castrado',
    status ENUM('disponivel', 'reservado', 'adotado') NOT NULL DEFAULT 'disponivel' COMMENT 'Status atual do pet',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_porte (porte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir alguns pets de exemplo
INSERT INTO pets (nome, idade, porte, sexo, raca, descricao, vacinado, castrado, status) VALUES
('Thor', '2 anos', 'Grande', 'M', 'Labrador Mix', 'Thor é um cachorro super carinhoso e brincalhão. Adora passeios e é ótimo com crianças!', 1, 1, 'disponivel'),
('Luna', '1 ano', 'Médio', 'F', 'Vira-lata Caramelo', 'Luna é muito dócil e calma. Perfeita para apartamento e ótima companhia!', 1, 1, 'disponivel'),
('Bob', '6 meses', 'Pequeno', 'M', 'SRD', 'Bob é um filhotinho cheio de energia! Está aprendendo comandos básicos e adora brincar.', 1, 0, 'disponivel'),
('Mel', '3 anos', 'Médio', 'F', 'Beagle Mix', 'Mel foi resgatada das ruas e é muito grata. Amorosa e companheira, ideal para família!', 1, 1, 'disponivel'),
('Rex', '4 anos', 'Grande', 'M', 'Pastor Alemão Mix', 'Rex é um guardião leal e protetor. Ideal para casa com quintal amplo.', 1, 1, 'disponivel'),
('Nina', '8 meses', 'Pequeno', 'F', 'Poodle Mix', 'Nina é brincalhona e adora colo! Hipoalergênica, perfeita para quem tem alergia.', 1, 0, 'disponivel');

-- ============================================================
-- FIM DO SCRIPT DE ATUALIZAÇÃO
-- ============================================================
