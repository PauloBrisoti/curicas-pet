<?php
/**
 * ============================================================
 * FRONT CONTROLLER — index.php
 * ============================================================
 *
 * Ponto de entrada único da aplicação.
 * Suporta Clean URLs (/orcamento, /adocao, /blog, /blog/slug)
 * e mantém compatibilidade total com ?page= para o admin.
 *
 * SEGURANÇA:
 * - Page e action são sanitizados por regex antes de qualquer uso
 * - Paths de controller são construídos internamente (sem user input)
 * - 404/500 são tratados explicitamente
 * ============================================================
 */

define('APP_ROOT', __DIR__);

// Iniciar sessão e carregar helpers
require_once __DIR__ . '/config/functions.php';

// ── Detectar rota: URI limpa ou query string legada ──────────────
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestUri = '/' . trim($requestUri, '/');

// Mapa de Clean URLs → identificador interno de página
$cleanRoutes = [
    '/'            => 'home',
    '/orcamento'   => 'orcamento',
    '/adocao'      => 'adocao',
    '/blog'        => 'blog',
    '/galeria'     => 'galeria',
    '/privacidade' => 'privacidade',
    '/whatsapp'    => 'whatsapp',   // Página Ponte — tráfego Instagram/links bio
    '/camiseta'    => 'camiseta',   // Página Ponte — campanha QR Code em camiseta
    '/avaliar'     => 'avaliar',    // Atalho 301 — avaliações Google Meu Negócio
];

// Inicializar $page como null — será definido abaixo
$page = null;

// ── PRIORIDADE 1: Rota de artigo de blog: /blog/{slug} ——————————
if (preg_match('#^/blog/([a-z0-9][a-z0-9\-]{0,80})$#i', $requestUri, $matches)) {
    $page         = 'artigo';
    $_GET['slug'] = $matches[1];

// ── PRIORIDADE 2: Query string explícita (?page=X) ————————————
// DEVE vir ANTES das clean routes porque a URI é '/' tanto para
// a home quanto para '/?page=admin' — sem isso, cleanRoutes['/'] = 'home'
// engolia qualquer ?page= (o admin nunca era alcançado).
} elseif (isset($_GET['page']) && $_GET['page'] !== '') {
    $rawPage = $_GET['page'];
    $page    = preg_replace('/[^a-z0-9_\-]/', '', strtolower($rawPage));

// ── PRIORIDADE 3: Admin via Clean URL /admin —————————————
} elseif (preg_match('#^/admin(/.*)?$#', $requestUri, $matches)) {
    $page = 'admin';
    if (!empty($matches[1])) {
        $actionFromUri = trim($matches[1], '/');
        if ($actionFromUri !== '' && !isset($_GET['action'])) {
            $_GET['action'] = preg_replace('/[^a-zA-Z0-9_\-]/', '', $actionFromUri);
        }
    }

// ── PRIORIDADE 4: Clean URLs conhecidas (/orcamento, /adocao…) ———
} elseif (isset($cleanRoutes[$requestUri])) {
    $page = $cleanRoutes[$requestUri];

// ── PRIORIDADE 5: Fallback → home ————————————————————————
} else {
    $page = 'home';
}

// Sanitizar action
$action = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['action'] ?? 'index');

// ── Mapa de páginas → Controllers ────────────────────────────────
$pageControllerMap = [
    'home'        => 'HomeController',
    'orcamento'   => 'OrcamentoController',
    'adocao'      => 'AdocaoController',
    'admin'       => 'AdminController',
    'blog'        => 'BlogController',
    'artigo'      => 'BlogController',
    // Páginas estáticas (sem controller)
    'galeria'     => null,
    'privacidade' => null,
];

// ── Páginas estáticas (include direto) ───────────────────────────
if ($page === 'privacidade') {
    $flash = getFlashMessage();
    require __DIR__ . '/views/privacidade.php';
    exit;
}

if ($page === 'galeria') {
    $flash = getFlashMessage();
    // Carregar model de galeria se existir
    $galeriaItems = [];
    if (file_exists(__DIR__ . '/models/Gallery.php')) {
        require_once __DIR__ . '/models/Gallery.php';
        $galleryModel = new Gallery();
        $galeriaItems = $galleryModel->listarAtivas() ?? [];
    }
    require __DIR__ . '/views/galeria.php';
    exit;
}

if ($page === 'whatsapp') {
    require __DIR__ . '/views/whatsapp.php';
    exit;
}

if ($page === 'camiseta') {
    require __DIR__ . '/views/camiseta.php';
    exit;
}

if ($page === 'avaliar') {
    require __DIR__ . '/views/avaliar.php';
    exit;
}

// ── 404: página desconhecida ─────────────────────────────────────
if (!isset($pageControllerMap[$page])) {
    http_response_code(404);
    $flash = getFlashMessage();
    require __DIR__ . '/views/404.php';
    exit;
}

// ── Mapa de actions por controller ───────────────────────────────
$actionMap = [
    'admin' => [
        'index'                  => 'login',
        'login'                  => 'login',
        'logout'                 => 'logout',
        'dashboard'              => 'dashboard',
        // Serviços
        'servicos'               => 'servicos',
        'servico-novo'           => 'servicoNovo',
        'servico-editar'         => 'servicoEditar',
        'servico-salvar'         => 'servicoSalvar',
        'servico-toggle'         => 'servicoToggle',
        'servico-toggle-ocultar' => 'servicoToggleOcultar',
        'servico-excluir'        => 'servicoExcluir',
        // Cotações
        'cotacoes'               => 'cotacoes',
        'cotacao-detalhe'        => 'cotacaoDetalhe',
        'cotacao-status'         => 'cotacaoStatus',
        // Pets
        'pets'                   => 'pets',
        'pet-novo'               => 'petNovo',
        'pet-editar'             => 'petEditar',
        'pet-salvar'             => 'petSalvar',
        'pet-excluir'            => 'petExcluir',
        // Blog
        'blog'                   => 'blog',
        'blog_form'              => 'blogForm',
        'blog_salvar'            => 'blogSalvar',
        'blog_excluir'           => 'blogExcluir',
        // Galeria
        'gallery'                => 'gallery',
        'gallery_form'           => 'galleryForm',
        'gallery_salvar'         => 'gallerySalvar',
        'gallery_excluir'        => 'galleryExcluir',
        // Configurações
        'configuracoes'          => 'configuracoes',
        'configuracoesSalvar'    => 'configuracoesSalvar',
    ],
    'orcamento' => [
        'index'   => 'index',
        'calcular' => 'calcular',
        'salvar'  => 'salvar',
    ],
    'adocao' => [
        'index'  => 'index',
    ],
    'blog' => [
        'index'  => 'index',
    ],
    'artigo' => [
        'index'  => 'artigo',
    ],
    'home' => [
        'index'  => 'index',
    ],
];

// Resolver action para o método correto do controller
if (isset($actionMap[$page][$action])) {
    $resolvedAction = $actionMap[$page][$action];
} else {
    // Tentar usar a action diretamente se existir no controller
    $resolvedAction = $action;
}

// ── Carregar e instanciar controller ─────────────────────────────
$controllerName = $pageControllerMap[$page];
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(500);
    error_log("Controller não encontrado: {$controllerName}");
    echo 'Erro interno do servidor.';
    exit;
}

require_once $controllerFile;
$controller = new $controllerName();

// Verificar se o método existe; fallback para index()
if (!method_exists($controller, $resolvedAction)) {
    if (method_exists($controller, 'index')) {
        $resolvedAction = 'index';
    } else {
        http_response_code(404);
        $flash = getFlashMessage();
        require __DIR__ . '/views/404.php';
        exit;
    }
}

// ── Executar ─────────────────────────────────────────────────────
try {
    $controller->$resolvedAction();
} catch (Throwable $e) {
    error_log('Erro fatal na aplicação: ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    } else {
        echo 'Erro interno do servidor. Por favor, tente novamente.';
    }
}
