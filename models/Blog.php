<?php
/**
 * ============================================================
 * MODEL: BLOG
 * ============================================================
 * 
 * Gerencia operações de banco de dados para posts do blog.
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

class Blog
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os posts ativos (para página pública)
     */
    public function listarAtivos(int $limite = 10): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM blog_posts 
                WHERE ativo = 1 
                ORDER BY created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limite]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Erro ao listar posts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna todos os posts (para admin)
     */
    public function listarTodos(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Erro ao listar posts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca um post pelo ID
     */
    public function buscarPorId(int $id): ?array
    {
        if (!$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM blog_posts WHERE id = ?");
            $stmt->execute([$id]);
            $post = $stmt->fetch();
            return $post ?: null;
        } catch (PDOException $e) {
            error_log('Erro ao buscar post: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca um post pelo slug
     */
    public function buscarPorSlug(string $slug): ?array
    {
        if (!$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND ativo = 1");
            $stmt->execute([$slug]);
            $post = $stmt->fetch();

            // Incrementa views
            if ($post) {
                $this->incrementarViews($post['id']);
            }

            return $post ?: null;
        } catch (PDOException $e) {
            error_log('Erro ao buscar post: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Incrementa contador de visualizações
     */
    private function incrementarViews(int $id): void
    {
        try {
            $stmt = $this->db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Erro ao incrementar views: ' . $e->getMessage());
        }
    }

    /**
     * Cria um novo post
     */
    public function criar(array $dados): int|false
    {
        if (!$this->db) {
            return false;
        }

        try {
            $slug = $this->gerarSlug($dados['titulo']);

            $stmt = $this->db->prepare("
                INSERT INTO blog_posts (titulo, slug, resumo, conteudo, imagem_destaque, meta_description, ativo)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $dados['titulo'],
                $slug,
                $dados['resumo'] ?? null,
                $dados['conteudo'],
                $dados['imagem_destaque'] ?? null,
                $dados['meta_description'] ?? null,
                $dados['ativo'] ?? 1
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Erro ao criar post: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza um post existente
     */
    public function atualizar(int $id, array $dados): bool
    {
        if (!$this->db) {
            return false;
        }

        try {
            $campos = [];
            $valores = [];

            foreach (['titulo', 'resumo', 'conteudo', 'imagem_destaque', 'meta_description', 'ativo'] as $campo) {
                if (isset($dados[$campo])) {
                    $campos[] = "$campo = ?";
                    $valores[] = $dados[$campo];
                }
            }

            // Atualiza slug se título mudou
            if (isset($dados['titulo'])) {
                $campos[] = "slug = ?";
                $valores[] = $this->gerarSlug($dados['titulo']);
            }

            if (empty($campos)) {
                return false;
            }

            $valores[] = $id;
            $sql = "UPDATE blog_posts SET " . implode(', ', $campos) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($valores);
        } catch (PDOException $e) {
            error_log('Erro ao atualizar post: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um post
     */
    public function excluir(int $id): bool
    {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM blog_posts WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Erro ao excluir post: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Conta total de posts
     */
    public function contarTotal(): int
    {
        if (!$this->db) {
            return 0;
        }

        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM blog_posts");
            return (int) $stmt->fetch()['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Conta posts ativos
     */
    public function contarAtivos(): int
    {
        if (!$this->db) {
            return 0;
        }

        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM blog_posts WHERE ativo = 1");
            return (int) $stmt->fetch()['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Gera slug a partir do título
     */
    private function gerarSlug(string $titulo): string
    {
        $slug = mb_strtolower($titulo);
        $slug = preg_replace('/[áàãâä]/u', 'a', $slug);
        $slug = preg_replace('/[éèêë]/u', 'e', $slug);
        $slug = preg_replace('/[íìîï]/u', 'i', $slug);
        $slug = preg_replace('/[óòõôö]/u', 'o', $slug);
        $slug = preg_replace('/[úùûü]/u', 'u', $slug);
        $slug = preg_replace('/[ç]/u', 'c', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    /**
     * Retorna posts mais lidos
     */
    public function maisLidos(int $limite = 5): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM blog_posts 
                WHERE ativo = 1 
                ORDER BY views DESC
                LIMIT ?
            ");
            $stmt->execute([$limite]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
