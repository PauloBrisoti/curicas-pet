<?php
/**
 * ============================================================
 * VIEW: WHATSAPP BRIDGE PAGE — Layout 100% Independente
 * ============================================================
 * Página Ponte de alta conversão para tráfego vindo do Instagram
 * e links bio. Não carrega nav nem footer do base.php.
 * Todo o HTML é autossuficiente neste arquivo.
 *
 * URL: curicaspet.com.br/whatsapp
 * ============================================================
 */

// Configs
$businessConfig = require __DIR__ . '/../config/business.php';
$appConfig      = require __DIR__ . '/../config/app.php';

$whatsappLink = 'https://wa.me/' . $businessConfig['whatsapp_num']
    . '?text=' . rawurlencode('Olá! Vim pelo Instagram e gostaria de saber mais sobre os serviços da Curicas Pet. 🐾');

$pageTitle       = 'Fale conosco no WhatsApp | Curicas Pet';
$pageDescription = 'Inicie uma conversa com a equipe Curicas Pet no WhatsApp. Saiba sobre Hotel Pet, Creche, Dog Walker, Pet Sitter e Asilo em Osasco.';
$pageUrl         = rtrim($businessConfig['site_url'] ?? 'https://curicaspet.com.br', '/') . '/whatsapp';
$logoUrl         = '/assets/images/logo-curicas-pet.png';
$ogImage         = $businessConfig['og_image'];
$nomeEmpresa     = $businessConfig['nome_empresa'];

// Função de escape seguro (inline, sem depender de functions.php carregado antes)
$safe = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $safe($pageTitle) ?></title>
    <meta name="description" content="<?= $safe($pageDescription) ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= $safe($pageUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="<?= $safe($pageTitle) ?>">
    <meta property="og:description" content="<?= $safe($pageDescription) ?>">
    <meta property="og:url"         content="<?= $safe($pageUrl) ?>">
    <meta property="og:image"       content="<?= $safe($ogImage) ?>">
    <meta property="og:site_name"   content="<?= $safe($nomeEmpresa) ?>">
    <meta property="og:locale"      content="pt_BR">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon.png">
    <link rel="shortcut icon"         type="image/png" href="/assets/images/favicon.png">

    <!-- Font Awesome (ícone WhatsApp) — assíncrono -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>

    <!-- Bootstrap Icons (ícones secundários) — assíncrono -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" as="style"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"></noscript>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* ── Reset mínimo ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', system-ui, sans-serif;
            background: linear-gradient(160deg, #1a2a0a 0%, #2d4a14 50%, #1a2a0a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }

        /* ── Card centralizado ── */
        .bridge-card {
            background: #fff;
            border-radius: 28px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 450px;           /* travado em 450px no desktop */
            box-shadow: 0 24px 80px rgba(0,0,0,.40);
        }

        /* ── Logo ── */
        .bridge-logo {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            object-fit: contain;
            background: #f0f7e8;
            padding: 8px;
            display: block;
            margin: 0 auto 1rem;
        }

        /* ── Identidade ── */
        .bridge-brand {
            font-size: 1.45rem;
            font-weight: 700;
            color: #1a2a0a;
            letter-spacing: -.02em;
            text-align: center;
            margin-bottom: .25rem;
        }
        .bridge-brand span { color: #2D6A0A; }

        .bridge-greeting {
            font-size: 1rem;
            color: #5a6a52;
            line-height: 1.5;
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .bridge-greeting small {
            font-size: .82rem;
            color: #7a8a72;
            display: block;
            margin-top: .25rem;
        }

        /* ── Botão WhatsApp gigante ── */
        .btn-whatsapp-giant {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            width: 100%;
            padding: 1rem 1.5rem;
            background: #25D366;
            color: #fff;
            border: none;
            border-radius: 16px;
            font-family: inherit;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(37,211,102,.35);
            transition: background .2s ease, transform .15s ease, box-shadow .2s ease;
            margin-bottom: 1.5rem;
        }
        .btn-whatsapp-giant:hover,
        .btn-whatsapp-giant:focus {
            background: #1da851;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(37,211,102,.45);
            outline: 3px solid rgba(37,211,102,.4);
            outline-offset: 2px;
        }
        .btn-whatsapp-giant i { font-size: 1.35rem; }

        /* ── Divisor ── */
        .bridge-divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #a0aa98;
            margin-bottom: 1rem;
        }
        .bridge-divider::before,
        .bridge-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e8f0e2;
        }

        /* ── Links secundários ── */
        .btn-bridge-secondary {
            display: flex;
            align-items: center;
            gap: .65rem;
            width: 100%;
            padding: .8rem 1.1rem;
            border: 1.5px solid #d4e4c8;
            border-radius: 14px;
            background: #f8fbf5;
            color: #2D6A0A;
            font-family: inherit;
            font-size: .88rem;
            font-weight: 600;
            text-decoration: none;
            transition: border-color .2s, background .2s, transform .15s;
            margin-bottom: .75rem;
        }
        .btn-bridge-secondary:last-of-type { margin-bottom: 0; }
        .btn-bridge-secondary:hover,
        .btn-bridge-secondary:focus {
            border-color: #4A9B2E;
            background: #eef6e6;
            color: #1a2a0a;
            transform: translateY(-1px);
            outline: 3px solid rgba(74,155,46,.3);
            outline-offset: 2px;
        }
        .btn-bridge-secondary .icon-main {
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .btn-bridge-secondary .btn-texts {
            flex: 1;
        }
        /* h2 semântico dos títulos dos cards — visualmente menor */
        .btn-bridge-secondary .btn-texts h2 {
            font-size: .88rem;
            font-weight: 600;
            color: inherit;
            line-height: 1.3;
            margin: 0;
        }
        .btn-bridge-secondary .btn-texts .sub    { font-size: .75rem; font-weight: 400; color: #6a8a5a; display: block; margin-top: .1rem; }
        .btn-bridge-secondary .chevron {
            font-size: .8rem;
            color: #9ab88a;
            flex-shrink: 0;
        }

        /* ── Rodapé discreto ── */
        .bridge-footnote {
            text-align: center;
            font-size: .72rem;
            color: #9aaa92;
            margin-top: 1.5rem;
        }

        /* ── Mobile: card full-width sem padding excessivo ── */
        @media (max-width: 480px) {
            body { padding: 1rem .75rem; justify-content: flex-start; }
            .bridge-card {
                border-radius: 20px;
                padding: 2rem 1.25rem;
            }
        }
    </style>
</head>

<body>

    <main class="bridge-card" role="main" aria-label="Página de contato Curicas Pet">

        <!-- Identidade da Marca -->
        <img src="<?= $safe($logoUrl) ?>"
             alt="Curicas Pet — Cuidados pet em Osasco"
             class="bridge-logo"
             width="80" height="80"
             fetchpriority="high"
             decoding="async">

        <h1 class="bridge-brand">
            <span>Curicas</span> Pet
        </h1>

        <p class="bridge-greeting">
            Que bom ter você aqui! 🐾
            <small>Hotel Pet · Creche · Dog Walker · Pet Sitter · Asilo</small>
        </p>

        <!-- Ação Principal -->
        <a href="<?= $safe($whatsappLink) ?>"
           target="_blank"
           rel="noopener noreferrer"
           id="btn-whatsapp-principal"
           class="btn-whatsapp-giant"
           aria-label="Iniciar conversa no WhatsApp com a Curicas Pet">
            <i class="fab fa-whatsapp" aria-hidden="true"></i>
            Iniciar conversa no WhatsApp
        </a>

        <!-- Separador -->
        <div class="bridge-divider" aria-hidden="true">ou explore o site</div>

        <!-- ── CARD 1: Hotel Pet ── -->
        <a href="/#hospedagem-hotel"
           id="btn-hotel-pet"
           class="btn-bridge-secondary"
           title="Conhecer o Hotel Pet da Curicas Pet em Osasco"
           aria-label="Conhecer o Hotel Pet em Osasco">
            <i class="bi bi-house-heart-fill icon-main" aria-hidden="true" style="color:#4A9B2E;"></i>
            <span class="btn-texts">
                <h2>Hotel Pet</h2>
                <span class="sub">Hospedagem segura e acolhedora em Osasco</span>
            </span>
            <i class="bi bi-chevron-right chevron" aria-hidden="true"></i>
        </a>

        <!-- ── CARD 2: Creche Diurna ── -->
        <a href="/#creche-daycare"
           id="btn-creche"
           class="btn-bridge-secondary"
           title="Creche Diurna para cães em Osasco"
           aria-label="Conhecer a Creche Diurna da Curicas Pet em Osasco">
            <i class="bi bi-sun-fill icon-main" aria-hidden="true" style="color:#F0A500;"></i>
            <span class="btn-texts">
                <h2>Creche Diurna</h2>
                <span class="sub">Socialização e cuidado sem gaiolas durante o dia</span>
            </span>
            <i class="bi bi-chevron-right chevron" aria-hidden="true"></i>
        </a>

        <!-- ── CARD 3: Dog Walker ── -->
        <a href="/#dog-walker"
           id="btn-dog-walker"
           class="btn-bridge-secondary"
           title="Passeios educativos com Dog Walker em Osasco"
           aria-label="Conhecer o serviço de Dog Walker com GPS em Osasco">
            <i class="bi bi-geo-alt-fill icon-main" aria-hidden="true" style="color:#2D6A0A;"></i>
            <span class="btn-texts">
                <h2>Passeios Educativos (Dog Walker)</h2>
                <span class="sub">Gasto de energia e socialização para o seu cão</span>
            </span>
            <i class="bi bi-chevron-right chevron" aria-hidden="true"></i>
        </a>

        <!-- ── CARD 4: Pet Sitter ── -->
        <a href="/#pet-sitter"
           id="btn-pet-sitter"
           class="btn-bridge-secondary"
           title="Pet Sitter profissional em Osasco"
           aria-label="Conhecer o serviço de Pet Sitter da Curicas Pet">
            <i class="bi bi-shield-heart-fill icon-main" aria-hidden="true" style="color:#5B8DD9;"></i>
            <span class="btn-texts">
                <h2>Pet Sitter</h2>
                <span class="sub">Cuidado profissional e carinho na segurança do lar do seu pet</span>
            </span>
            <i class="bi bi-chevron-right chevron" aria-hidden="true"></i>
        </a>

        <!-- ── CARD 5: Asilo Pet ── -->
        <a href="/#asilo-canino"
           id="btn-asilo-pet"
           class="btn-bridge-secondary"
           title="Asilo Pet para cães e gatos idosos em Osasco"
           aria-label="Conhecer o Asilo Pet da Curicas Pet para pets idosos">
            <i class="bi bi-heart-pulse-fill icon-main" aria-hidden="true" style="color:#e05c7a;"></i>
            <span class="btn-texts">
                <h2>Asilo Pet</h2>
                <span class="sub">Estrutura dedicada e cuidados especiais para pets idosos</span>
            </span>
            <i class="bi bi-chevron-right chevron" aria-hidden="true"></i>
        </a>

        <!-- Rodapé discreto -->
        <p class="bridge-footnote">
            📍 Osasco, SP — atendemos Zona Oeste e região
        </p>

    </main>

</body>
</html>
