<?php
/**
 * ============================================================
 * MODEL: COTAÇÃO / ORÇAMENTO
 * ============================================================
 * 
 * Gerencia operações de banco de dados para a tabela 'cotacoes'.
 * Armazena solicitações de orçamento dos clientes.
 * 
 * ESTRUTURA DE servicos_selecionados (JSON):
 * [
 *   {"servico_id": 1, "nome": "Hotel", "quantidade": 5, "preco_unitario": 120.00, "subtotal": 600.00},
 *   {"servico_id": 2, "nome": "Walking", "quantidade": 3, "preco_unitario": 45.00, "subtotal": 135.00}
 * ]
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

class Cotacao
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ============================================================
    // MÉTODOS DE CRIAÇÃO (Frontend)
    // ============================================================

    /**
     * Cria uma nova cotação
     * 
     * @param array $data Dados da cotação
     * @return int ID da cotação criada
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO cotacoes (
                nome_cliente, email, telefone, servicos_selecionados,
                quantidade_pets, valor_estimado, desconto_aplicado, valor_final, mensagem
            ) VALUES (
                :nome_cliente, :email, :telefone, :servicos_selecionados,
                :quantidade_pets, :valor_estimado, :desconto_aplicado, :valor_final, :mensagem
            )
        ");

        $stmt->execute([
            'nome_cliente' => $data['nome_cliente'],
            'email' => $data['email'],
            'telefone' => $data['telefone'] ?? null,
            'servicos_selecionados' => json_encode($data['servicos_selecionados'], JSON_UNESCAPED_UNICODE),
            'quantidade_pets' => $data['quantidade_pets'],
            'valor_estimado' => $data['valor_estimado'],
            'desconto_aplicado' => $data['desconto_aplicado'],
            'valor_final' => $data['valor_final'],
            'mensagem' => $data['mensagem'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    // ============================================================
    // MÉTODOS DE LEITURA (Admin)
    // ============================================================

    /**
     * Retorna todas as cotações ordenadas por data
     * 
     * @param int $limit Limite de resultados (0 = sem limite)
     * @param int $offset Offset para paginação
     * @return array Lista de cotações
     */
    public function getAll(int $limit = 0, int $offset = 0): array
    {
        $sql = "
            SELECT id, nome_cliente, email, telefone, servicos_selecionados,
                   quantidade_pets, valor_estimado, desconto_aplicado, valor_final,
                   mensagem, status, created_at
            FROM cotacoes
            ORDER BY created_at DESC
        ";

        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit > 0) {
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        $cotacoes = $stmt->fetchAll();

        // Decodificar JSON dos serviços
        foreach ($cotacoes as &$cotacao) {
            $cotacao['servicos_selecionados'] = json_decode($cotacao['servicos_selecionados'], true) ?? [];
        }

        return $cotacoes;
    }

    /**
     * Retorna cotações filtradas por status
     * 
     * @param string $status Status desejado
     * @return array Lista de cotações
     */
    public function getByStatus(string $status): array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome_cliente, email, telefone, servicos_selecionados,
                   quantidade_pets, valor_estimado, desconto_aplicado, valor_final,
                   mensagem, status, created_at
            FROM cotacoes
            WHERE status = :status
            ORDER BY created_at DESC
        ");
        $stmt->execute(['status' => $status]);
        $cotacoes = $stmt->fetchAll();

        foreach ($cotacoes as &$cotacao) {
            $cotacao['servicos_selecionados'] = json_decode($cotacao['servicos_selecionados'], true) ?? [];
        }

        return $cotacoes;
    }

    /**
     * Busca uma cotação pelo ID
     * 
     * @param int $id ID da cotação
     * @return array|false Dados da cotação ou false
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT id, nome_cliente, email, telefone, servicos_selecionados,
                   quantidade_pets, valor_estimado, desconto_aplicado, valor_final,
                   mensagem, status, created_at, updated_at
            FROM cotacoes
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        $cotacao = $stmt->fetch();

        if ($cotacao) {
            $cotacao['servicos_selecionados'] = json_decode($cotacao['servicos_selecionados'], true) ?? [];
        }

        return $cotacao;
    }

    // ============================================================
    // MÉTODOS DE ATUALIZAÇÃO (Admin)
    // ============================================================

    /**
     * Atualiza o status de uma cotação
     * 
     * @param int $id ID da cotação
     * @param string $status Novo status
     * @return bool Sucesso da operação
     */
    public function updateStatus(int $id, string $status): bool
    {
        $validStatuses = ['pendente', 'respondida', 'convertida', 'cancelada'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE cotacoes
            SET status = :status
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }

    /**
     * Exclui uma cotação
     * 
     * @param int $id ID da cotação
     * @return bool Sucesso da operação
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM cotacoes WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // ============================================================
    // MÉTODOS ESTATÍSTICOS (Admin Dashboard)
    // ============================================================

    /**
     * Conta cotações por status
     * 
     * @return array Array associativo [status => count]
     */
    public function countByStatus(): array
    {
        $stmt = $this->db->query("
            SELECT status, COUNT(*) as total
            FROM cotacoes
            GROUP BY status
        ");
        $results = $stmt->fetchAll();

        $counts = [
            'pendente' => 0,
            'respondida' => 0,
            'convertida' => 0,
            'cancelada' => 0
        ];

        foreach ($results as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    /**
     * Conta total de cotações
     * 
     * @return int Total de cotações
     */
    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM cotacoes");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Soma o valor total de cotações convertidas
     * 
     * @return float Valor total
     */
    public function sumConvertidas(): float
    {
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(valor_final), 0) as total
            FROM cotacoes
            WHERE status = 'convertida'
        ");
        return (float) $stmt->fetchColumn();
    }

    /**
     * Retorna cotações recentes (últimas 7 dias)
     * 
     * @param int $limit Limite de resultados
     * @return array Lista de cotações
     */
    public function getRecentes(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome_cliente, email, valor_final, status, created_at
            FROM cotacoes
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
