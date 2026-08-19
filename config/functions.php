<?php
/**
 * ============================================================
 * FUNÇÕES DE SEGURANÇA E UTILITÁRIOS
 * ============================================================
 * 
 * Este arquivo contém todas as funções de segurança da aplicação:
 * - Prevenção de XSS (Cross-Site Scripting)
 * - Proteção CSRF (Cross-Site Request Forgery)
 * - Sanitização e validação de dados
 * 
 * IMPORTANTE: Sempre inclua este arquivo antes de processar dados!
 * ============================================================
 */

// Inicia a sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    // Configurações de segurança da sessão
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1); // Apenas HTTPS
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// ============================================================
// PREVENÇÃO DE XSS (Cross-Site Scripting)
// ============================================================

/**
 * Escapa caracteres especiais para exibição segura em HTML
 * USE ESTA FUNÇÃO EM TODA SAÍDA DE DADOS DO BANCO PARA A TELA!
 * 
 * @param mixed $data Dado a ser escapado
 * @return string Dado seguro para exibição
 * 
 * Exemplo de uso:
 * echo safe($servico['nome']); // Previne <script>alert('XSS')</script>
 */
function safe(mixed $data): string
{
    if ($data === null) {
        return '';
    }
    return htmlspecialchars((string) $data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Escapa array recursivamente
 * Útil para escapar arrays inteiros de uma vez
 * 
 * @param array $array Array a ser escapado
 * @return array Array com valores escapados
 */
function safeArray(array $array): array
{
    return array_map(function ($value) {
        if (is_array($value)) {
            return safeArray($value);
        }
        return safe($value);
    }, $array);
}

/**
 * Renderiza HTML confiável gerado pelo painel admin.
 *
 * Diferença de safe():
 * - safe()     → escapa TUDO (usa em dados do usuário / inputs externos)
 * - safeHtml() → permite tags semânticas seguras (usa em conteúdo do ADMIN)
 *
 * Tags permitidas: <strong> <em> <br> <ul> <ol> <li> <p> <h2> <h3> <a> <span>
 * Atributos perigosos (onclick, onerror, href="javascript:") são removidos.
 *
 * @param mixed $data  Conteúdo HTML vindo do banco (admin)
 * @return string      HTML higienizado
 */
function safeHtml(mixed $data): string
{
    if ($data === null) {
        return '';
    }

    $html = (string) $data;

    // Tags permitidas no conteúdo de serviços / blog
    $allowedTags = '<strong><em><b><i><br><ul><ol><li><p><h2><h3><h4><span><a>';

    // Remover tudo fora da lista de tags permitidas
    $html = strip_tags($html, $allowedTags);

    // Remover atributos perigosos (event handlers e javascript:)
    $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
    $html = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/i', '', $html);

    return $html;
}

// ============================================================
// PROTEÇÃO CSRF (Cross-Site Request Forgery)
// ============================================================

/**
 * Gera um token CSRF único e armazena na sessão
 * Use em todos os formulários POST administrativos
 * 
 * @return string Token CSRF
 * 
 * Exemplo de uso no formulário:
 * <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
 */
function generateCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida o token CSRF enviado pelo formulário
 * Chame no início do processamento de formulários POST
 * 
 * @param string|null $token Token enviado pelo formulário
 * @return bool True se válido, False se inválido
 * 
 * Exemplo de uso:
 * if (!validateCSRFToken($_POST['csrf_token'] ?? null)) {
 *     die('Token de segurança inválido');
 * }
 */
function validateCSRFToken(?string $token): bool
{
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenera o token CSRF
 * Chame após processar um formulário com sucesso
 */
function regenerateCSRFToken(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Retorna o campo hidden HTML do CSRF
 * Forma mais prática de incluir o token no formulário
 * 
 * @return string Campo hidden HTML
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

// ============================================================
// SANITIZAÇÃO E VALIDAÇÃO DE DADOS
// ============================================================

/**
 * Sanitiza e valida dados de entrada conforme o tipo especificado
 * Use antes de processar qualquer dado do usuário
 * 
 * @param mixed $data Dado a ser sanitizado
 * @param string $type Tipo esperado: 'int', 'float', 'email', 'string', 'bool'
 * @return mixed Dado sanitizado ou null se inválido
 * 
 * Exemplos:
 * $id = sanitize($_GET['id'], 'int');           // Retorna int ou null
 * $preco = sanitize($_POST['preco'], 'float');  // Retorna float ou null
 * $email = sanitize($_POST['email'], 'email');  // Retorna email válido ou null
 */
function sanitize(mixed $data, string $type): mixed
{
    if ($data === null || $data === '') {
        return null;
    }

    switch ($type) {
        case 'int':
            $filtered = filter_var($data, FILTER_VALIDATE_INT);
            return $filtered !== false ? $filtered : null;

        case 'float':
            // Aceita vírgula como separador decimal (padrão BR)
            $data = str_replace(',', '.', (string) $data);
            $filtered = filter_var($data, FILTER_VALIDATE_FLOAT);
            return $filtered !== false ? $filtered : null;

        case 'email':
            $filtered = filter_var($data, FILTER_VALIDATE_EMAIL);
            return $filtered !== false ? strtolower($filtered) : null;

        case 'string':
            return trim(strip_tags((string) $data));

        case 'bool':
            return filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        case 'phone':
            // Remove tudo exceto números
            $phone = preg_replace('/[^0-9]/', '', (string) $data);
            return strlen($phone) >= 10 ? $phone : null;

        default:
            return trim((string) $data);
    }
}

/**
 * Valida se todos os campos obrigatórios estão preenchidos
 * 
 * @param array $data Dados do formulário
 * @param array $required Lista de campos obrigatórios
 * @return array Lista de campos faltantes
 */
function validateRequired(array $data, array $required): array
{
    $missing = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
            $missing[] = $field;
        }
    }
    return $missing;
}

// ============================================================
// FUNÇÕES DE RESPOSTA E REDIRECIONAMENTO
// ============================================================

/**
 * Redireciona para uma URL com mensagem flash
 * 
 * @param string $url URL de destino
 * @param string $message Mensagem a exibir
 * @param string $type Tipo: 'success', 'error', 'warning', 'info'
 */
function redirect(string $url, string $message = '', string $type = 'info'): never
{
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type']   = $type;
    }

    // REGRA: nunca incluir 'index.php' na URL de redirect.
    // O .htaccess tem uma regra que redireciona /index.php?... para /
    // e isso causava loop silencioso na rota do admin.
    // Substitui 'index.php?page=admin' por '/?page=admin' etc.
    $url = preg_replace('#^index\.php\?#', '/?', $url);
    $url = preg_replace('#^index\.php$#', '/',   $url);

    // Garantir que a URL seja absoluta para evitar interpretação
    // relativa pelo Apache (que poderia reescrever de forma inesperada)
    if (!preg_match('#^https?://#', $url)) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Não usar dirname(SCRIPT_NAME): em subpastas da Hostinger
        // isso gera caminhos incorretos. Usar apenas o host + url.
        if ($url[0] !== '/') {
            $url = '/' . $url;
        }

        $url = $scheme . '://' . $host . $url;
    }

    // Gravar qualquer dado de sessão (flash, admin_id, etc.) no disco
    // ANTES de enviar o header Location.
    // Crítico em servidores de hosting compartilhado (Hostinger) onde
    // a sessão é armazenada em arquivo e pode não ser salva a tempo.
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    header('Location: ' . $url, true, 302);
    exit;
}

/**
 * Obtém e limpa a mensagem flash da sessão
 * 
 * @return array|null Array com 'message' e 'type' ou null
 */
function getFlashMessage(): ?array
{
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type'] ?? 'info'
        ];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return $flash;
    }
    return null;
}

/**
 * Retorna resposta JSON (para AJAX)
 * 
 * @param array $data Dados a retornar
 * @param int $statusCode Código HTTP
 */
function jsonResponse(array $data, int $statusCode = 200): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================================
// FUNÇÕES DE AUTENTICAÇÃO
// ============================================================

/**
 * Verifica se o usuário está logado como admin
 * 
 * @return bool True se logado
 */
function isAdminLoggedIn(): bool
{
    return isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0;
}

/**
 * Requer autenticação de admin
 * Redireciona para login se não estiver logado
 */
function requireAdminAuth(): void
{
    if (!isAdminLoggedIn()) {
        redirect('/?page=admin&action=login', 'Faça login para acessar.', 'warning');
    }
}

/**
 * Obtém o ID do admin logado
 * 
 * @return int|null ID do admin ou null
 */
function getAdminId(): ?int
{
    return $_SESSION['admin_id'] ?? null;
}
