-- ============================================================
-- GALLERY TABLE UPDATE - Suporte a Vídeos e Ordenação
-- ============================================================
-- Execute este SQL no phpMyAdmin da Hostinger
-- ============================================================

-- Adicionar coluna para tipo (foto/video)
ALTER TABLE gallery 
ADD COLUMN IF NOT EXISTS tipo ENUM('foto', 'video') DEFAULT 'foto' AFTER imagem;

-- Adicionar coluna para ordenação
ALTER TABLE gallery 
ADD COLUMN IF NOT EXISTS ordem INT DEFAULT 0 AFTER tipo;

-- Criar índice para ordenação
ALTER TABLE gallery 
ADD INDEX IF NOT EXISTS idx_ordem (ordem);

-- Verificar estrutura atualizada
-- DESCRIBE gallery;
