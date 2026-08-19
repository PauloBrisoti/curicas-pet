<?php
/**
 * ============================================================
 * MODEL: GALLERY
 * ============================================================
 * 
 * Gerencia operações de banco de dados para a galeria de fotos.
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

class Gallery
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todas as fotos/videos ativas para carrossel (ordenado)
     */
    public function listarCarrossel(int $limite = 10): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM gallery 
                WHERE ativo = 1 
                ORDER BY ordem ASC, created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limite]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Erro ao listar carrossel: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna todas as fotos ativas (para página pública)
     */
    public function listarAtivas(int $limite = 50): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM gallery 
                WHERE ativo = 1 
                ORDER BY ordem ASC, created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limite]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Erro ao listar fotos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna todas as fotos (para admin)
     */
    public function listarTodas(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->query("SELECT * FROM gallery ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Erro ao listar fotos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca uma foto pelo ID
     */
    public function buscarPorId(int $id): ?array
    {
        if (!$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            $foto = $stmt->fetch();
            return $foto ?: null;
        } catch (PDOException $e) {
            error_log('Erro ao buscar foto: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cria uma nova foto/video
     */
    public function criar(array $dados): int|false
    {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO gallery (titulo, descricao, imagem, tipo, ordem, ativo)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $dados['titulo'],
                $dados['descricao'] ?? null,
                $dados['imagem'],
                $dados['tipo'] ?? 'foto',
                $dados['ordem'] ?? 0,
                $dados['ativo'] ?? 1
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Erro ao criar foto: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza uma foto/video existente
     */
    public function atualizar(int $id, array $dados): bool
    {
        if (!$this->db) {
            return false;
        }

        try {
            $campos = [];
            $valores = [];

            foreach (['titulo', 'descricao', 'imagem', 'tipo', 'ordem', 'ativo'] as $campo) {
                if (isset($dados[$campo])) {
                    $campos[] = "$campo = ?";
                    $valores[] = $dados[$campo];
                }
            }

            if (empty($campos)) {
                return false;
            }

            $valores[] = $id;
            $sql = "UPDATE gallery SET " . implode(', ', $campos) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($valores);
        } catch (PDOException $e) {
            error_log('Erro ao atualizar foto: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui uma foto
     */
    public function excluir(int $id): bool
    {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM gallery WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Erro ao excluir foto: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Conta total de fotos
     */
    public function contarTotal(): int
    {
        if (!$this->db) {
            return 0;
        }

        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM gallery");
            return (int) $stmt->fetch()['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}
