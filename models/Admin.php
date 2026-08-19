<?php
/**
 * ============================================================
 * MODEL: ADMINISTRADOR
 * ============================================================
 * 
 * Gerencia operações de autenticação de administradores.
 * Usa password_hash() e password_verify() para segurança.
 * 
 * IMPORTANTE: Nunca armazene senhas em texto plano!
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

class Admin
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ============================================================
    // MÉTODOS DE AUTENTICAÇÃO
    // ============================================================

    /**
     * Autentica um administrador por email e senha
     * 
     * @param string $email Email do admin
     * @param string $senha Senha em texto plano
     * @return array|false Dados do admin ou false se inválido
     */
    public function autenticar(string $email, string $senha): array|false
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, senha_hash, ativo
            FROM administradores
            WHERE email = :email AND ativo = 1
        ");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();

        if (!$admin) {
            return false;
        }

        // Verificar senha usando password_verify
        if (!password_verify($senha, $admin['senha_hash'])) {
            return false;
        }

        // Atualizar último login
        $this->atualizarUltimoLogin($admin['id']);

        // Remover hash da senha do retorno por segurança
        unset($admin['senha_hash']);

        return $admin;
    }

    /**
     * Atualiza o timestamp do último login
     * 
     * @param int $id ID do admin
     */
    private function atualizarUltimoLogin(int $id): void
    {
        $stmt = $this->db->prepare("
            UPDATE administradores
            SET ultimo_login = NOW()
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Busca um admin pelo ID
     * 
     * @param int $id ID do admin
     * @return array|false Dados do admin ou false
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, ativo, ultimo_login, created_at
            FROM administradores
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // ============================================================
    // MÉTODOS DE GERENCIAMENTO (Para futuras expansões)
    // ============================================================

    /**
     * Cria um novo administrador
     * 
     * @param array $data Dados do admin (nome, email, senha)
     * @return int ID do admin criado
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO administradores (nome, email, senha_hash, ativo)
            VALUES (:nome, :email, :senha_hash, 1)
        ");

        $stmt->execute([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha_hash' => password_hash($data['senha'], PASSWORD_DEFAULT)
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza a senha de um administrador
     * 
     * @param int $id ID do admin
     * @param string $novaSenha Nova senha em texto plano
     * @return bool Sucesso da operação
     */
    public function atualizarSenha(int $id, string $novaSenha): bool
    {
        $stmt = $this->db->prepare("
            UPDATE administradores
            SET senha_hash = :senha_hash
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'senha_hash' => password_hash($novaSenha, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Verifica se um email já está cadastrado
     * 
     * @param string $email Email a verificar
     * @param int|null $excludeId ID a excluir (para update)
     * @return bool True se existe
     */
    public function emailExiste(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM administradores WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
