<?php
/**
 * ============================================================
 * MODEL: PET
 * ============================================================
 * 
 * Gerencia operações de banco de dados para pets de adoção.
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

class Pet
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os pets disponíveis para adoção
     */
    public function listarDisponiveis(): array
    {
        if (!$this->db) {
            return $this->getDadosEstaticos();
        }

        try {
            $stmt = $this->db->query("
                SELECT * FROM pets 
                WHERE status = 'disponivel' 
                ORDER BY created_at DESC
            ");
            $pets = $stmt->fetchAll();
            return !empty($pets) ? $pets : $this->getDadosEstaticos();
        } catch (PDOException $e) {
            error_log('Erro ao listar pets: ' . $e->getMessage());
            return $this->getDadosEstaticos();
        }
    }

    /**
     * Retorna todos os pets (para admin)
     */
    public function listarTodos(): array
    {
        if (!$this->db) {
            return $this->getDadosEstaticos();
        }

        try {
            $stmt = $this->db->query("SELECT * FROM pets ORDER BY status ASC, created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Erro ao listar pets: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca um pet pelo ID
     */
    public function buscarPorId(int $id): ?array
    {
        if (!$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM pets WHERE id = ?");
            $stmt->execute([$id]);
            $pet = $stmt->fetch();
            return $pet ?: null;
        } catch (PDOException $e) {
            error_log('Erro ao buscar pet: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cria um novo pet
     */
    public function criar(array $dados): int|false
    {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO pets (nome, idade, porte, sexo, raca, descricao, imagem, vacinado, castrado, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $dados['nome'],
                $dados['idade'],
                $dados['porte'],
                $dados['sexo'],
                $dados['raca'] ?? 'SRD',
                $dados['descricao'],
                $dados['imagem'] ?? null,
                $dados['vacinado'] ?? 1,
                $dados['castrado'] ?? 0,
                $dados['status'] ?? 'disponivel'
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Erro ao criar pet: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza um pet existente
     */
    public function atualizar(int $id, array $dados): bool
    {
        if (!$this->db) {
            return false;
        }

        try {
            $campos = [];
            $valores = [];

            foreach (['nome', 'idade', 'porte', 'sexo', 'raca', 'descricao', 'imagem', 'vacinado', 'castrado', 'status'] as $campo) {
                if (isset($dados[$campo])) {
                    $campos[] = "$campo = ?";
                    $valores[] = $dados[$campo];
                }
            }

            if (empty($campos)) {
                return false;
            }

            $valores[] = $id;
            $sql = "UPDATE pets SET " . implode(', ', $campos) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($valores);
        } catch (PDOException $e) {
            error_log('Erro ao atualizar pet: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um pet
     */
    public function excluir(int $id): bool
    {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM pets WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Erro ao excluir pet: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Conta pets por status
     */
    public function contarPorStatus(): array
    {
        if (!$this->db) {
            return ['disponivel' => 6, 'reservado' => 0, 'adotado' => 0];
        }

        try {
            $stmt = $this->db->query("
                SELECT status, COUNT(*) as total 
                FROM pets 
                GROUP BY status
            ");
            $resultados = $stmt->fetchAll();

            $contagem = ['disponivel' => 0, 'reservado' => 0, 'adotado' => 0];
            foreach ($resultados as $row) {
                $contagem[$row['status']] = (int) $row['total'];
            }
            return $contagem;
        } catch (PDOException $e) {
            error_log('Erro ao contar pets: ' . $e->getMessage());
            return ['disponivel' => 0, 'reservado' => 0, 'adotado' => 0];
        }
    }

    /**
     * Dados estáticos para fallback (sem banco de dados)
     */
    private function getDadosEstaticos(): array
    {
        return [
            [
                'id' => 1,
                'nome' => 'Thor',
                'idade' => '2 anos',
                'porte' => 'Grande',
                'sexo' => 'M',
                'raca' => 'Labrador Mix',
                'descricao' => 'Thor é um cachorro super carinhoso e brincalhão. Adora passeios e é ótimo com crianças!',
                'imagem' => null,
                'vacinado' => 1,
                'castrado' => 1,
                'status' => 'disponivel'
            ],
            [
                'id' => 2,
                'nome' => 'Luna',
                'idade' => '1 ano',
                'porte' => 'Médio',
                'sexo' => 'F',
                'raca' => 'Vira-lata Caramelo',
                'descricao' => 'Luna é muito dócil e calma. Perfeita para apartamento e ótima companhia!',
                'imagem' => null,
                'vacinado' => 1,
                'castrado' => 1,
                'status' => 'disponivel'
            ],
            [
                'id' => 3,
                'nome' => 'Bob',
                'idade' => '6 meses',
                'porte' => 'Pequeno',
                'sexo' => 'M',
                'raca' => 'SRD',
                'descricao' => 'Bob é um filhotinho cheio de energia! Está aprendendo comandos básicos e adora brincar.',
                'imagem' => null,
                'vacinado' => 1,
                'castrado' => 0,
                'status' => 'disponivel'
            ],
            [
                'id' => 4,
                'nome' => 'Mel',
                'idade' => '3 anos',
                'porte' => 'Médio',
                'sexo' => 'F',
                'raca' => 'Beagle Mix',
                'descricao' => 'Mel foi resgatada das ruas e é muito grata. Amorosa e companheira, ideal para família!',
                'imagem' => null,
                'vacinado' => 1,
                'castrado' => 1,
                'status' => 'disponivel'
            ],
            [
                'id' => 5,
                'nome' => 'Rex',
                'idade' => '4 anos',
                'porte' => 'Grande',
                'sexo' => 'M',
                'raca' => 'Pastor Alemão Mix',
                'descricao' => 'Rex é um guardião leal e protetor. Ideal para casa com quintal amplo.',
                'imagem' => null,
                'vacinado' => 1,
                'castrado' => 1,
                'status' => 'disponivel'
            ],
            [
                'id' => 6,
                'nome' => 'Nina',
                'idade' => '8 meses',
                'porte' => 'Pequeno',
                'sexo' => 'F',
                'raca' => 'Poodle Mix',
                'descricao' => 'Nina é brincalhona e adora colo! Hipoalergênica, perfeita para quem tem alergia.',
                'imagem' => null,
                'vacinado' => 1,
                'castrado' => 0,
                'status' => 'disponivel'
            ]
        ];
    }
}
