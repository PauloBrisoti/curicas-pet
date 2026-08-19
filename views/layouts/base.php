<?php
/**
 * ============================================================
 * LAYOUT BASE — v4.0
 * ============================================================
 * - Motor de metadados dinâmicos por página (SEO Siloing)
 * - Schema LocalBusiness E-E-A-T com areaServed Osasco
 * - Breadcrumb HTML oculto via CSS; JSON-LD intacto para Googlebot
 * - Canonical absoluta e limpa por rota
 * ============================================================
 */

$businessConfig = require __DIR__ . '/../../config/business.php';

// ── Detectar página atual PRIMEIRO (necessário para o SEO map) ────────────────
$_currentPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$_getPage     = $_GET['page'] ?? '';

$currentPage = match(true) {
    str_starts_with($_currentPath, 'admin') || $_getPage === 'admin' => 'admin',
    $_currentPath === '' || $_currentPath === 'index.php'            => 'home',
    str_starts_with($_currentPath, 'orcamento')                      => 'orcamento',
    str_starts_with($_currentPath, 'adocao')                         => 'adocao',
    str_starts_with($_currentPath, 'blog/')                          => 'artigo',
    str_starts_with($_currentPath, 'blog')                           => 'blog',
    str_starts_with($_currentPath, 'galeria')                        => 'galeria',
    str_starts_with($_currentPath, 'privacidade')                    => 'privacidade',
    str_starts_with($_currentPath, 'whatsapp')                       => 'whatsapp',
    str_starts_with($_currentPath, 'camiseta')                       => 'camiseta',
    str_starts_with($_currentPath, 'avaliar')                        => 'avaliar',
    default                                                           => ($_getPage ?: 'home'),
};

// ── Motor de Metadados Dinâmicos por Página ───────────────────────────────────
// Cada entrada: [title, description (~150 chars), canonical_path]
// Views podem SOBRESCREVER $pageTitle/$pageDescription antes de incluir este arquivo.
$_seoMap = [
    'home' => [
        'title'       => 'Curicas Pet — Asilo, Creche, Hotel Pet e Dog Walker em Osasco | Zona Oeste SP',
        'description' => 'Dog Walker com GPS, Asilo Canino especializado, Creche sem gaiolas e Hotel Pet em Osasco. Cuidado premium para seu pet. Solicite orçamento pelo WhatsApp.',
        'path'        => '/',
    ],
    'orcamento' => [
        'title'       => 'Orçamento de Serviços Pet em Osasco | Curicas Pet',
        'description' => 'Solicite orçamento para Dog Walker, Asilo Canino, Creche, Hotel Pet e Pet Sitter em Osasco e Zona Oeste SP. Resposta em até 2h pelo WhatsApp.',
        'path'        => '/orcamento',
    ],
    'adocao' => [
        'title'       => 'Adote um Pet em Osasco | Serviço Solidário | Curicas Pet',
        'description' => 'Adote um cão resgatado em Osasco. Conheça os pets disponíveis e apoie nossa causa animal em Campesina, Adalgisa e Vila Yara na Zona Oeste de SP.',
        'path'        => '/adocao',
    ],
    'blog' => [
        'title'       => 'Blog Pet: Dicas para Tutores em Osasco e Zona Oeste | Curicas Pet',
        'description' => 'Artigos e guias sobre cuidados com pets para tutores em Osasco, Pinheiros, Lapa e Zona Oeste SP. Conteúdo escrito por profissionais especializados.',
        'path'        => '/blog',
    ],
    'artigo' => [
        // Views de artigo devem definir $pageTitle e $pageDescription antes de incluir base.php
        'title'       => ($pageTitle       ?? 'Artigo | Blog Curicas Pet'),
        'description' => ($pageDescription ?? 'Leia o artigo completo no Blog da Curicas Pet — dicas de cuidados para pets em Osasco e Zona Oeste SP.'),
        'path'        => '/' . ltrim($_currentPath, '/'),
    ],
    'galeria' => [
        'title'       => 'Galeria de Fotos | Curicas Pet Osasco',
        'description' => 'Veja fotos e vídeos dos passeios, asilo canino, creche e hotel pet da Curicas Pet em Osasco. Cuidado com amor registrado dia a dia.',
        'path'        => '/galeria',
    ],
    'privacidade' => [
        'title'       => 'Política de Privacidade | Curicas Pet',
        'description' => 'Leia a política de privacidade e termos de uso da Curicas Pet. Seus dados são protegidos com segurança e responsabilidade conforme a LGPD.',
        'path'        => '/privacidade',
    ],
    'admin' => [
        'title'       => 'Painel Administrativo | Curicas Pet',
        'description' => '',
        'path'        => '',
    ],
    'whatsapp' => [
        'title'       => 'Fale conosco no WhatsApp | Curicas Pet',
        'description' => 'Inicie uma conversa com a equipe Curicas Pet no WhatsApp. Tire dúvidas, solicite orçamentos e saiba mais sobre Dog Walker, Hotel Pet e Adoção em Osasco.',
        'path'        => '/whatsapp',
    ],
    'camiseta' => [
        'title'       => 'Contatos e Serviços | Curicas Pet — Hotel, Creche e Dog Walker em Osasco',
        'description' => 'Conheça a Curicas Pet em Osasco: Dog Walker com GPS, Hotel Pet, Creche sem gaiolas, Banho e Tosa, e muito mais. Fale conosco agora pelo WhatsApp!',
        'path'        => '/camiseta',
    ],
    'avaliar' => [
        'title'       => 'Avaliar no Google | Curicas Pet',
        'description' => 'Avalie a Curicas Pet no Google. Sua opinião nos ajuda a continuar oferecendo o melhor cuidado pet em Osasco.',
        'path'        => '/avaliar',
    ],
];

$_seo = $_seoMap[$currentPage] ?? $_seoMap['home'];

// Resolve metadados: views que definem $pageTitle antes de incluir têm prioridade
$pageTitle       = (isset($pageTitle)       && $pageTitle       !== '') ? $pageTitle       : $_seo['title'];
$pageDescription = (isset($pageDescription) && $pageDescription !== '') ? $pageDescription : $_seo['description'];

// Canonical absoluta e limpa (nunca com ?utm_* ou &fbclid)
$_siteUrl = rtrim($businessConfig['site_url'], '/');
if ($currentPage === 'artigo') {
    $pageUrl = $_siteUrl . '/' . ltrim($_currentPath, '/');
} elseif (!isset($pageUrl) || empty($pageUrl) || $pageUrl === $businessConfig['site_url']) {
    $pageUrl = $_siteUrl . $_seo['path'];
}

$pageImage = $pageImage ?? $businessConfig['og_image'];
$isAdmin   = $isAdmin   ?? false;
$bodyClass = $bodyClass ?? '';

// Links globais
$whatsappLink = 'https://wa.me/' . $businessConfig['whatsapp_num'] . '?text=' . rawurlencode($businessConfig['whatsapp_msg']);
$mapsLink     = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode('Praça Padroeira do Brasil, 111, Centro, Osasco, SP');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title><?= safe($pageTitle) ?></title>
    <meta name="description" content="<?= safe($pageDescription) ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="<?= safe($businessConfig['nome_empresa']) ?>">
    <link rel="canonical" href="<?= safe($pageUrl) ?>">

    <!-- Favicon -->
    <link rel="icon"             type="image/png" sizes="32x32"  href="/assets/images/favicon.png">
    <link rel="icon"             type="image/png" sizes="192x192" href="/assets/images/favicon-192.png">
    <link rel="apple-touch-icon"                  sizes="180x180" href="/assets/images/apple-touch-icon.png">
    <link rel="shortcut icon"    type="image/png"                href="/assets/images/favicon.png">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= safe($pageTitle) ?>">
    <meta property="og:description" content="<?= safe($pageDescription) ?>">
    <meta property="og:image" content="<?= safe($pageImage) ?>">
    <meta property="og:url" content="<?= safe($pageUrl) ?>">
    <meta property="og:site_name" content="<?= safe($businessConfig['nome_empresa']) ?>">
    <meta property="og:locale" content="pt_BR">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= safe($pageTitle) ?>">
    <meta name="twitter:description" content="<?= safe($pageDescription) ?>">
    <meta name="twitter:image" content="<?= safe($pageImage) ?>">

    <!-- Geo Tags (SEO Local) -->
    <meta name="geo.region" content="BR-<?= safe($businessConfig['estado']) ?>">
    <meta name="geo.placename" content="<?= safe($businessConfig['cidade']) ?>">
    <meta name="geo.position" content="<?= safe($businessConfig['geo_latitude']) ?>;<?= safe($businessConfig['geo_longitude']) ?>">
    <meta name="ICBM" content="<?= safe($businessConfig['geo_latitude']) ?>, <?= safe($businessConfig['geo_longitude']) ?>">

    <!-- Preload da imagem LCP — garante download antecipado antes do parser chegar ao body -->
    <link rel="preload" as="image" href="assets/images/hero-dog.webp" fetchpriority="high">

    <!-- Bootstrap 5 CSS — servido localmente (sem latência de CDN externo) -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">

    <!-- Bootstrap Icons — assíncrono (aperços visuais não críticos para layout) -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" as="style"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"></noscript>

    <!-- Font Awesome — assíncrono (apenás ícones decorativos) -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>

    <!-- Google Fonts — display=swap para evitar FOIT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:        #6BBF2D;
            --primary-light:  #8FDC4F;
            --primary-dark:   #4A9B2E;
            --dark-bg:        #1e2d0f;
            --nav-bg:         #1a2a0a;
            --body-bg:        #F8F9FA;
            --card-shadow:    0 4px 24px rgba(0,0,0,.07);
            --radius:         16px;
            --whatsapp:       #25D366;
            --instagram-grad: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);
        }

        /* ── Base ── */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            color: #1a2a0a;
        }

        /* ── Navbar ── */
        .navbar {
            background: var(--nav-bg);
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            color: #fff !important;
        }

        .nav-link {
            font-weight: 500;
            color: rgba(255,255,255,.75) !important;
            transition: color .25s ease;
            padding: .5rem .85rem !important;
            border-radius: 8px;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-light) !important;
            background: rgba(143,220,79,.1);
        }

        /* Mobile collapse */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: #111f07;
                margin: 8px -15px -10px;
                padding: 12px 20px 16px;
                border-radius: 12px;
            }
        }

        /* Social buttons navbar */
        .social-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            transition: transform .25s, opacity .25s;
            text-decoration: none;
        }
        .social-btn:hover { transform: scale(1.12); color: #fff; opacity: .9; }
        .social-btn.whatsapp  { background: var(--whatsapp); }
        .social-btn.instagram { background: var(--instagram-grad); }
        .social-btn.facebook  { background: #1877F2; }

        /* ── Botões ── */
        /*
         * Animações GPU-composited apenas: transform + opacity.
         * Removido: box-shadow, border-color, color do transition
         * (essas propriedades disparam layout/paint no Main Thread).
         */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            border: none;
            border-radius: 50px;
            font-weight: 600;
            /* will-change avisa o browser para criar camada composta antecipadamente */
            will-change: transform;
            transition: transform .25s ease, opacity .25s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            opacity: .92;
        }
        .btn-outline-primary {
            /* Cor escurecida para contraste WCAG 4.5:1 contra fundo branco */
            border: 2px solid #2D6A0A;
            color: #2D6A0A;
            border-radius: 50px;
            font-weight: 600;
            will-change: transform;
            transition: transform .25s ease, opacity .25s ease;
        }
        .btn-outline-primary:hover {
            /* Sem mudar border-color/color no hover — usa opacity para feedback visual */
            transform: translateY(-2px);
            opacity: .85;
        }
        .btn-whatsapp {
            background: var(--whatsapp);
            border: none;
            color: #fff;
            border-radius: 50px;
            font-weight: 600;
            will-change: transform;
            transition: transform .25s ease, opacity .25s ease;
        }
        .btn-whatsapp:hover {
            color: #fff;
            transform: translateY(-2px);
            opacity: .9;
        }
        .btn-success {
            border-radius: 50px;
            font-weight: 600;
            will-change: transform;
            transition: transform .25s ease, opacity .25s ease;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            opacity: .9;
        }

        /* ── Cards ── */
        .card {
            border-radius: var(--radius) !important;
            /* box-shadow removido do transition — causa compositing layer extra no mobile */
            will-change: transform;
            transition: transform .3s ease;
        }
        .card:hover {
            transform: translateY(-4px);
        }

        /* ── Forms ── */
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            transition: border-color .25s, box-shadow .25s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(143,220,79,.15);
        }

        /* ── Animations ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in           { animation: fadeInUp .6s ease forwards; }
        .delay-1                   { animation-delay: .1s; }
        .delay-2                   { animation-delay: .2s; }
        .delay-3                   { animation-delay: .3s; }

        /* ── Floating WhatsApp ── */
        .floating-whatsapp {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 58px;
            height: 58px;
            background: var(--whatsapp);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.75rem;
            box-shadow: 0 6px 20px rgba(37,211,102,.4);
            z-index: 1000;
            transition: transform .3s, box-shadow .3s;
            text-decoration: none;
        }
        .floating-whatsapp:hover {
            transform: scale(1.1);
            color: #fff;
            box-shadow: 0 10px 30px rgba(37,211,102,.5);
        }
        @media (max-width: 576px) {
            .floating-whatsapp { bottom: 80px; }
        }

        /* ── Footer ── */
        footer {
            background: var(--nav-bg);
            color: #8a9a7a;
            padding: 64px 0 28px;
        }
        footer h6   { color: #fff; margin-bottom: 1rem; font-weight: 600; }
        footer a    { color: #8a9a7a; text-decoration: none; transition: color .25s; }
        footer a:hover { color: var(--primary-light); }
        .footer-address { color: rgba(255,255,255,.65); }

        .social-icon-lg {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            transition: transform .3s, opacity .3s;
            text-decoration: none;
        }
        .social-icon-lg:hover { transform: translateY(-4px); color: #fff; opacity: .9; }

        /* ── Admin Sidebar ── */
        .admin-sidebar {
            background: var(--nav-bg);
            min-height: 100vh;
            padding-top: 1rem;
        }
        .admin-sidebar .nav-link { color: #94a3b8; padding: 12px 20px; border-radius: 8px; margin: 4px 12px; }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active { background: rgba(143,220,79,.18); color: #fff; }
        .admin-sidebar .nav-link i { margin-right: 10px; }

        /* Stats cards */
        .stats-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }
        .stats-card .stats-number { font-size: 2rem; font-weight: 700; color: var(--nav-bg); }
        .stats-card .stats-icon   { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }

        /* Admin mobile */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                height: 100vh;
                z-index: 1040;
                transition: left .3s ease;
                width: 260px !important;
                min-width: 260px !important;
            }
            .admin-sidebar.show { left: 0; box-shadow: 5px 0 20px rgba(0,0,0,.3); }
            .admin-sidebar.show::after {
                content: '';
                position: fixed;
                top: 0;
                left: 260px;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,.5);
                z-index: -1;
            }
            .admin-content { padding-left: 15px !important; padding-top: 60px !important; }
            .stats-number  { font-size: 1.5rem !important; }
            .hide-mobile   { display: none !important; }
        }

        /* ── Cookie Consent ── */
        .cookie-consent {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(135deg, #1e293b, #334155);
            color: #fff;
            padding: 14px 20px;
            z-index: 9999;
            box-shadow: 0 -4px 20px rgba(0,0,0,.3);
        }
        .cookie-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .cookie-text  { flex: 1; font-size: .88rem; }
        .cookie-link  { color: var(--primary-light); text-decoration: underline; }
        .cookie-link:hover { color: #a5e86a; }
        @media (max-width: 576px) {
            .cookie-content { flex-direction: column; text-align: center; }
        }

        /* ── Descrição de serviço com HTML semântico (safeHtml) ── */
        .servico-descricao ul {
            list-style: disc;
            padding-left: 1.25rem;
            margin-bottom: .5rem;
        }
        .servico-descricao li {
            margin-bottom: .35rem;
            font-size: .95rem;
        }
        .servico-descricao strong { color: #2D3E1F; }
        .servico-descricao br + br { display: block; margin-top: .5rem; }

        /* ── Menu hambúrguer (mobile) — ícone branco sobre fundo escuro ── */
        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3) !important;
            padding: .35rem .55rem;
        }
        .navbar-toggler-icon {
            filter: invert(1) brightness(200%);
        }
        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(143, 220, 79, 0.4);
        }
    </style>
</head>

<body class="<?= safe($bodyClass) ?>">

    <?php if (!$isAdmin): ?>

    <?php
    // ── Breadcrumbs: calcular aqui, renderizar APÓS navbar ──────────
    $breadcrumbMap = [
        'home'        => [],
        'orcamento'   => [['Serviços em Osasco', '/#servicos'], ['Orçamento', '/orcamento']],
        'adocao'      => [['Adoção e Doação em Osasco', '/adocao']],
        'blog'        => [['Blog', '/blog']],
        'artigo'      => [['Blog', '/blog'], [$pageTitle, '#']],
        'galeria'     => [['Galeria', '/galeria']],
        'privacidade' => [['Privacidade', '/privacidade']],
    ];
    $breadcrumbs = $breadcrumbMap[$currentPage] ?? [];
    ?>

    <!-- Navbar Pública -->
    <nav class="navbar navbar-expand-lg sticky-top" role="navigation" aria-label="Menu principal">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <img src="assets/images/logo-curicas-pet.png"
                     alt="" aria-hidden="true"
                     width="42" height="42" style="border-radius:8px;">
                <span style="color:var(--primary-light);">Curicas</span>&nbsp;<span style="color:#fff;">Pet</span>
            </a>

            <button class="navbar-toggler border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navMain"
                    aria-controls="navMain" aria-expanded="false"
                    aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="/">
                            <i class="bi bi-house me-1"></i>Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'orcamento' ? 'active' : '' ?>" href="/orcamento">
                            <i class="bi bi-chat-dots me-1"></i>Orçamento
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'adocao' ? 'active' : '' ?>" href="/adocao">
                            <i class="bi bi-heart me-1"></i>Adote um Pet
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'blog' ? 'active' : '' ?>" href="/blog">
                            <i class="bi bi-journal-text me-1"></i>Blog
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#contato">
                            <i class="bi bi-envelope me-1"></i>Contato
                        </a>
                    </li>
                </ul>

                <!-- Botões sociais -->
                <div class="d-flex gap-2 align-items-center">
                    <a href="<?= safe($whatsappLink) ?>" target="_blank" rel="noopener"
                       class="social-btn whatsapp" title="WhatsApp" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    </a>
                    <a href="<?= safe($businessConfig['instagram_url']) ?>" target="_blank" rel="noopener"
                       class="social-btn instagram" title="Instagram" aria-label="Instagram">
                        <i class="fab fa-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="<?= safe($businessConfig['facebook_url']) ?>" target="_blank" rel="noopener"
                       class="social-btn facebook" title="Facebook" aria-label="Facebook">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <?php if (!$isAdmin && !empty($breadcrumbs)): ?>
    <!-- Breadcrumbs — estrutura semântica para Googlebot; oculto visualmente via CSS -->
    <nav aria-label="Breadcrumb" id="site-breadcrumb"
         style="display:none !important;">
        <div class="container">
            <ol class="breadcrumb mb-0"
                itemscope itemtype="https://schema.org/BreadcrumbList"
                style="padding:0; background:none; font-size:.82rem;">
                <li class="breadcrumb-item"
                    itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="/" itemprop="item" style="color:#4A9B2E; text-decoration:none;">
                        <span itemprop="name">Início</span>
                    </a>
                    <meta itemprop="position" content="1">
                </li>
                <?php foreach ($breadcrumbs as $idx => $crumb): ?>
                <?php if ($crumb[1] === '#' || $idx === count($breadcrumbs) - 1): ?>
                <li class="breadcrumb-item active"
                    itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                    aria-current="page" style="color:#6c757d;">
                    <span itemprop="name"><?= safe($crumb[0]) ?></span>
                    <meta itemprop="position" content="<?= $idx + 2 ?>">
                </li>
                <?php else: ?>
                <li class="breadcrumb-item"
                    itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="<?= safe($crumb[1]) ?>" itemprop="item"
                       style="color:#4A9B2E; text-decoration:none;">
                        <span itemprop="name"><?= safe($crumb[0]) ?></span>
                    </a>
                    <meta itemprop="position" content="<?= $idx + 2 ?>">
                </li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Conteúdo Principal -->
    <main role="main" id="conteudo-principal">
        <?= $content ?? '' ?>
    </main>

    <?php if (!$isAdmin): ?>
    <!-- ── FOOTER PÚBLICO GEO-OTIMIZADO ── -->
    <footer id="contato" role="contentinfo">
        <div class="container">
            <div class="row g-5">

                <!-- Coluna 1: Marca + Bairros atendidos (sinal geo-local) -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="assets/images/logo-curicas-pet.png"
                             alt="" aria-hidden="true"
                             width="52" height="52"
                             class="bg-white rounded p-1">
                        <div>
                            <div class="fw-bold text-white lh-1"><?= safe($businessConfig['nome_empresa']) ?></div>
                            <small><?= safe($businessConfig['slogan']) ?></small>
                        </div>
                    </div>
                    <p class="small mb-2">Dog Walker &bull; Asilo Canino &bull; Creche &bull; Hotel Pet &bull; Pet Sitter</p>
                    <p class="small" style="color:rgba(255,255,255,.55);">
                        Atendimento especializado em Osasco:
                        Centro, Campesina, Adalgisa, Bela Vista, Vila Yara e região.
                    </p>
                </div>

                <!-- Coluna 2: Serviços em Osasco (linkagem interna para crawl) -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h4 class="h6">Serviços em Osasco</h4>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="/#dog-walker">Dog Walker com GPS</a></li>
                        <li class="mb-2"><a href="/#hospedagem-hotel">Hotel Pet e Hospedagem</a></li>
                        <li class="mb-2"><a href="/#asilo-canino">Asilo Canino</a></li>
                        <li class="mb-2"><a href="/#creche-daycare">Creche e Daycare</a></li>
                        <li class="mb-2"><a href="/#pet-sitter">Pet Sitter</a></li>
                        <li class="mb-2"><a href="/orcamento">Orçamento</a></li>
                        <li class="mb-2"><a href="/adocao">Adoção</a></li>
                        <li class="mb-2"><a href="/blog">Blog</a></li>
                    </ul>
                </div>

                <!-- Coluna 3: Contato com <address> semântico -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="h6">Contato</h4>
                    <address class="small footer-address" style="font-style:normal;">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-geo-alt me-2 text-success"></i>
                                <a href="<?= safe($mapsLink) ?>" target="_blank" rel="noopener"
                                   style="color:rgba(255,255,255,.65);">
                                    Praça Padroeira do Brasil, 111<br>
                                    <span class="ms-4">Centro &mdash; Osasco/SP</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-envelope me-2 text-success"></i>
                                <a href="mailto:<?= safe($businessConfig['email']) ?>"
                                   style="color:rgba(255,255,255,.65);">
                                    <?= safe($businessConfig['email']) ?>
                                </a>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-whatsapp me-2 text-success"></i>
                                <a href="<?= safe($whatsappLink) ?>" target="_blank"
                                   style="color:rgba(255,255,255,.65);">
                                    <?= safe($businessConfig['telefone_display']) ?>
                                </a>
                            </li>
                        </ul>
                    </address>
                </div>

                <!-- Coluna 4: Redes + CTA -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="h6">Redes Sociais</h4>
                    <div class="d-flex gap-2 mb-3">
                        <a href="<?= safe($whatsappLink) ?>" target="_blank" rel="noopener"
                           class="social-icon-lg" style="background:var(--whatsapp);" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="<?= safe($businessConfig['instagram_url']) ?>" target="_blank" rel="noopener"
                           class="social-icon-lg" style="background:var(--instagram-grad);" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="<?= safe($businessConfig['facebook_url']) ?>" target="_blank" rel="noopener"
                           class="social-icon-lg" style="background:#1877F2;" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </div>
                    <a href="<?= safe($whatsappLink) ?>" target="_blank" rel="noopener"
                       class="btn btn-whatsapp btn-sm px-4">
                        <i class="fab fa-whatsapp me-2"></i>Fale Conosco
                    </a>
                </div>
            </div>

            <hr class="my-4" style="border-color: rgba(255,255,255,.1);">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small">
                <p class="mb-0" style="color:rgba(255,255,255,.7);">
                    &copy; <?= date('Y') ?> <?= safe($businessConfig['nome_empresa']) ?>.
                    Todos os direitos reservados.
                </p>
                <a href="/privacidade" style="color:rgba(255,255,255,.7);">
                    <i class="bi bi-shield-lock me-1"></i>Política de Privacidade
                </a>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp -->
    <a href="<?= safe($whatsappLink) ?>" target="_blank" rel="noopener"
       class="floating-whatsapp" title="WhatsApp" aria-label="Fale conosco pelo WhatsApp">
        <i class="fab fa-whatsapp" aria-hidden="true"></i>
    </a>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <!-- Bootstrap JS — servido localmente (sem latência de CDN externo) -->
    <script src="/assets/js/bootstrap.bundle.min.js" defer></script>

    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>

    <?php if (!$isAdmin): ?>
    <!-- Schema.org JSON-LD — LocalBusiness + E-E-A-T -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": ["LocalBusiness", "AnimalShelter"],
        "@id": "<?= safe($businessConfig['site_url']) ?>/#organization",
        "name": "<?= safe($businessConfig['nome_empresa']) ?>",
        "description": "Asilo canino especializado, Creche sem gaiolas, Hotel Pet e Dog Walker com GPS em Osasco, SP. Cuidado premium para seu pet por profissionais certificados.",
        "url": "<?= safe($businessConfig['site_url']) ?>",
        "logo": "<?= safe($businessConfig['logo_url']) ?>",
        "image": "<?= safe($businessConfig['og_image']) ?>",
        "telephone": "+55<?= preg_replace('/\D/', '', $businessConfig['whatsapp_num'] ?? '') ?>",
        "email": "<?= safe($businessConfig['email']) ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Praça Padroeira do Brasil, 111",
            "addressLocality": "Osasco",
            "addressRegion": "SP",
            "postalCode": "<?= safe($businessConfig['cep'] ?? '06010-090') ?>",
            "addressCountry": "BR"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "<?= safe($businessConfig['geo_latitude']) ?>",
            "longitude": "<?= safe($businessConfig['geo_longitude']) ?>"
        },
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
                "opens": "07:30",
                "closes": "19:30"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Saturday"],
                "opens": "09:30",
                "closes": "15:00"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Sunday"],
                "opens": "10:00",
                "closes": "14:00"
            }
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "customer service",
            "telephone": "+55<?= preg_replace('/\D/', '', $businessConfig['whatsapp_num'] ?? '') ?>",
            "contactOption": "TollFree",
            "availableLanguage": "Portuguese"
        },
        "sameAs": [
            "<?= safe($businessConfig['instagram_url']) ?>",
            "<?= safe($businessConfig['facebook_url']) ?>"
        ],
        "priceRange": "$$",
        "areaServed": [
            {"@type": "City", "name": "Osasco", "sameAs": "https://www.wikidata.org/wiki/Q486635"},
            {"@type": "Place", "name": "Centro, Osasco"},
            {"@type": "Place", "name": "Campesina, Osasco"},
            {"@type": "Place", "name": "Adalgisa, Osasco"},
            {"@type": "Place", "name": "Bela Vista, Osasco"},
            {"@type": "Place", "name": "Vila Yara, Osasco"},
            {"@type": "City", "name": "Pinheiros"},
            {"@type": "City", "name": "Lapa"},
            {"@type": "City", "name": "Boaçava"}
        ],
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Serviços Pet em Osasco, SP",
            "itemListElement": [
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Dog Walker em Osasco", "description": "Passeios supervisionados com GPS em tempo real para cães em Osasco e Zona Oeste SP"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Asilo Canino em Osasco", "description": "Cuidado especializado para cães idosos em Osasco, com equipe treinada e rotina serena"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Creche e Daycare em Osasco", "description": "Socialização supervisionada sem gaiolas para cães em Osasco"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Hotel Pet e Hospedagem em Osasco", "description": "Hospedagem segura e confortável sem gaiolas para cães e gatos em Osasco"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Pet Sitter em Osasco", "description": "Cuidador profissional em domicílio em Osasco, Pinheiros, Lapa, Boaçava e região"}}
            ]
        }
    }
    </script>

    <!-- Cookie Consent LGPD -->
    <div id="cookieConsent" class="cookie-consent" style="display:none;">
        <div class="cookie-content">
            <div class="cookie-text">
                <i class="bi bi-shield-check me-2"></i>
                Usamos cookies para melhorar sua experiência. Ao continuar, você concorda com nossa
                <a href="/privacidade" class="cookie-link">Política de Privacidade</a>.
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-light" onclick="declineCookies()">Recusar</button>
                <button class="btn btn-sm btn-light" onclick="acceptCookies()">Aceitar</button>
            </div>
        </div>
    </div>

    <script>
        // Cookie consent
        document.addEventListener('DOMContentLoaded', function () {
            if (!localStorage.getItem('cookieConsent')) {
                document.getElementById('cookieConsent').style.display = 'block';
            }
        });
        function acceptCookies()  { localStorage.setItem('cookieConsent','accepted');  document.getElementById('cookieConsent').style.display='none'; }
        function declineCookies() { localStorage.setItem('cookieConsent','declined');  document.getElementById('cookieConsent').style.display='none'; }

        // Fechar menu mobile ao clicar fora
        document.addEventListener('click', function (e) {
            const nav = document.querySelector('.navbar-collapse');
            const tog = document.querySelector('.navbar-toggler');
            if (nav && tog && nav.classList.contains('show') && !nav.contains(e.target) && !tog.contains(e.target)) {
                tog.click();
            }
        });

        // Fechar menu ao clicar em link
        document.querySelectorAll('.navbar-nav .nav-link').forEach(function (l) {
            l.addEventListener('click', function () {
                const nav = document.querySelector('.navbar-collapse');
                const tog = document.querySelector('.navbar-toggler');
                if (nav && nav.classList.contains('show')) tog.click();
            });
        });

        // Smooth scroll para âncoras
        document.querySelectorAll('a[href*="#"]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                const href      = this.getAttribute('href');
                const hashIndex = href.indexOf('#');
                if (hashIndex === -1) return;
                const target = document.getElementById(href.substring(hashIndex + 1));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
    <?php endif; ?>

</body>
</html>