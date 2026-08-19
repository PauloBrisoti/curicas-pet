<?php
/**
 * ============================================================
 * MODEL: SERVIÇO
 * ============================================================
 * 
 * Gerencia operações de banco de dados para a tabela 'servicos'.
 * Todas as queries usam Prepared Statements para segurança.
 * 
 * IMPORTANTE: Este model é usado tanto no frontend (listar ativos)
 * quanto no admin (CRUD completo).
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

class Servico
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ============================================================
    // MÉTODOS DE LEITURA (Frontend e Admin)
    // ============================================================

    /**
     * Retorna todos os serviços ativos
     * Use na página inicial e calculadora de orçamento
     * 
     * @return array Lista de serviços ativos
     */
    public function getAtivos(): array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, descricao, preco_base, ocultar_preco, icone_bootstrap, imagem_url, tipo_calculo
            FROM servicos
            WHERE ativo = 1
            ORDER BY nome ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Retorna todos os serviços (ativos e inativos)
     * Use apenas no painel admin
     * 
     * @return array Lista completa de serviços
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, descricao, preco_base, ocultar_preco, icone_bootstrap, imagem_url, tipo_calculo, ativo, created_at, updated_at
            FROM servicos
            ORDER BY ativo DESC, nome ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Busca um serviço pelo ID
     * 
     * @param int $id ID do serviço
     * @return array|false Dados do serviço ou false se não encontrado
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, descricao, preco_base, ocultar_preco, icone_bootstrap, imagem_url, tipo_calculo, ativo
            FROM servicos
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Busca múltiplos serviços por IDs
     * Útil para calcular orçamento com vários serviços selecionados
     * 
     * @param array $ids Array de IDs
     * @return array Lista de serviços
     */
    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        // Validar que todos são inteiros
        $ids = array_filter(array_map('intval', $ids));
        if (empty($ids)) {
            return [];
        }

        // Criar placeholders para IN clause
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->db->prepare("
            SELECT id, nome, descricao, preco_base, ocultar_preco, icone_bootstrap, imagem_url, tipo_calculo
            FROM servicos
            WHERE id IN ($placeholders) AND ativo = 1
        ");
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    // ============================================================
    // MÉTODOS DE ESCRITA (Apenas Admin)
    // ============================================================

    /**
     * Cria um novo serviço
     * 
     * @param array $data Dados do serviço
     * @return int ID do serviço criado
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO servicos (nome, descricao, preco_base, ocultar_preco, icone_bootstrap, imagem_url, tipo_calculo, ativo)
            VALUES (:nome, :descricao, :preco_base, :ocultar_preco, :icone_bootstrap, :imagem_url, :tipo_calculo, :ativo)
        ");

        $stmt->execute([
            'nome'            => $data['nome'],
            'descricao'       => $data['descricao'],
            'preco_base'      => $data['preco_base'],
            'ocultar_preco'   => $data['ocultar_preco'] ?? 0,
            'icone_bootstrap' => $data['icone_bootstrap'] ?? 'bi-star',
            'imagem_url'      => $data['imagem_url'] ?? null,
            'tipo_calculo'    => $data['tipo_calculo'] ?? 'diaria',
            'ativo'           => $data['ativo'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza um serviço existente
     * 
     * @param int $id ID do serviço
     * @param array $data Novos dados
     * @return bool Sucesso da operação
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE servicos
            SET nome = :nome,
                descricao = :descricao,
                preco_base = :preco_base,
                ocultar_preco = :ocultar_preco,
                icone_bootstrap = :icone_bootstrap,
                imagem_url = :imagem_url,
                tipo_calculo = :tipo_calculo,
                ativo = :ativo
            WHERE id = :id
        ");

        return $stmt->execute([
            'id'              => $id,
            'nome'            => $data['nome'],
            'descricao'       => $data['descricao'],
            'preco_base'      => $data['preco_base'],
            'ocultar_preco'   => $data['ocultar_preco'] ?? 0,
            'icone_bootstrap' => $data['icone_bootstrap'] ?? 'bi-star',
            'imagem_url'      => $data['imagem_url'] ?? null,
            'tipo_calculo'    => $data['tipo_calculo'] ?? 'diaria',
            'ativo'           => $data['ativo'] ?? 1
        ]);
    }

    /**
     * Alterna o status ativo/inativo de um serviço
     * 
     * @param int $id ID do serviço
     * @return bool Sucesso da operação
     */
    public function toggleAtivo(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE servicos
            SET ativo = NOT ativo
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Alterna o flag ocultar_preco de um serviço.
     * SEGURO: deve ser chamado apenas via POST com CSRF validado
     * pelo AdminController.
     *
     * @param int $id ID do serviço
     * @return bool Sucesso da operação
     */
    public function toggleOcultarPreco(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE servicos
            SET ocultar_preco = NOT ocultar_preco
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Exclui um serviço
     * CUIDADO: Exclusão permanente! Considere usar toggleAtivo() em vez disso.
     * 
     * @param int $id ID do serviço
     * @return bool Sucesso da operação
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM servicos WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // ============================================================
    // MÉTODOS AUXILIARES
    // ============================================================

    /**
     * Conta total de serviços ativos
     * 
     * @return int Total de serviços ativos
     */
    public function countAtivos(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM servicos WHERE ativo = 1");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Verifica se um serviço existe pelo nome
     * Útil para evitar duplicatas
     * 
     * @param string $nome Nome do serviço
     * @param int|null $excludeId ID a excluir da verificação (para update)
     * @return bool True se existe
     */
    public function existsByNome(string $nome, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM servicos WHERE nome = :nome";
        $params = ['nome' => $nome];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
