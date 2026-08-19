-- ============================================================
-- ATUALIZAÇÃO: Adicionar coluna de imagem aos serviços
-- ============================================================
-- Execute este comando no phpMyAdmin ou terminal MySQL
-- após ter criado o banco com o database.sql original
-- ============================================================

ALTER TABLE servicos
ADD COLUMN imagem_url VARCHAR(255) NULL DEFAULT NULL
COMMENT 'URL da imagem do serviço (opcional)'
AFTER icone_bootstrap;

-- ============================================================
-- ATUALIZAÇÃO: Adicionar imagens padrão aos serviços existentes
-- (Opcional - você pode substituir pelos links reais depois)
-- ============================================================

UPDATE servicos SET imagem_url = 'https://placehold.co/600x400/6366f1/white?text=Hotel+Pet' WHERE id = 1;
UPDATE servicos SET imagem_url = 'https://placehold.co/600x400/06b6d4/white?text=Dog+Walking' WHERE id = 2;
UPDATE servicos SET imagem_url = 'https://placehold.co/600x400/10b981/white?text=Pet+Sitter' WHERE id = 3;
