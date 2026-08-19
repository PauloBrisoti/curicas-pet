<?php
/**
 * ============================================================
 * CONFIGURAÇÃO DE CONEXÃO COM BANCO DE DADOS
 * ============================================================
 * 
 * INSTRUÇÕES PARA HOSTINGER:
 * 1. Acesse o hPanel da Hostinger
 * 2. Vá em "Banco de Dados" > "MySQL"
 * 3. Copie os dados de conexão para as constantes abaixo
 * 
 * IMPORTANTE: Nunca envie este arquivo para repositórios públicos!
 * ============================================================
 */

// ============================================================
// CONFIGURAÇÕES DO BANCO DE DADOS
// Altere os valores abaixo conforme seu ambiente
// ============================================================

// Host do banco de dados
// Hostinger geralmente usa: localhost ou mysql.hostinger.com
define('DB_HOST', 'localhost');

// Nome do banco de dados
// Hostinger - Banco de produção
define('DB_NAME', 'u378955289_curicas_pet');

// Usuário do banco de dados
// Hostinger - Usuário de produção
define('DB_USER', 'u378955289_curicas_us');

// Senha do banco de dados
// Hostinger - Senha de produção
define('DB_PASS', '6W^VJ:aC9jr');

// Charset da conexão (manter utf8mb4 para suportar emojis)
define('DB_CHARSET', 'utf8mb4');

// ============================================================
// CLASSE DE CONEXÃO PDO
// ============================================================

class Database
{
    private static ?PDO $instance = null;
    private static bool $connectionFailed = false;

    /**
     * Retorna uma instância única de conexão PDO (Singleton)
     * Usa Prepared Statements por padrão para segurança
     * 
     * MODO DEMO: Retorna null se não conseguir conectar,
     * permitindo que o site funcione com dados de demonstração
     */
    public static function getConnection(): ?PDO
    {
        // Se já tentou conectar e falhou, não tenta novamente
        if (self::$connectionFailed) {
            return null;
        }

        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_NAME,
                    DB_CHARSET
                );

                $options = [
                    // Lança exceções em caso de erro
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                    // Retorna resultados como array associativo
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                    // Desativa emulação de prepared statements (mais seguro)
                    PDO::ATTR_EMULATE_PREPARES => false,

                    // Timeout de conexão curto para não travar
                    PDO::ATTR_TIMEOUT => 3
                ];

                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);

                // Configurar fuso horário do MySQL para São Paulo (se suportado)
                try {
                    self::$instance->exec("SET time_zone = '-03:00'");
                } catch (Exception $e) {
                    // Ignora se o servidor não suportar
                }

            } catch (PDOException $e) {
                // Marca que a conexão falhou para não tentar novamente
                self::$connectionFailed = true;

                // Log do erro (não exibe para o usuário)
                error_log('Erro de conexão: ' . $e->getMessage());

                // Retorna null em vez de morrer - permite modo demo
                return null;
            }
        }

        return self::$instance;
    }

    /**
     * Verifica se há conexão com o banco disponível
     * Use para alternar entre dados reais e demo
     */
    public static function isConnected(): bool
    {
        return self::getConnection() !== null;
    }

    /**
     * Previne clonagem da instância
     */
    private function __clone()
    {
    }

    /**
     * Previne deserialização
     */
    public function __wakeup()
    {
        throw new Exception("Não é possível deserializar singleton");
    }
}

