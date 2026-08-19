<?php
/**
 * ============================================================
 * SITEMAP.XML DINÂMICO — Curicas Pet
 * ============================================================
 *
 * Gera um sitemap válido para o Google Search Console.
 * Inclui automaticamente os artigos do blog publicados.
 *
 * URL pública: https://curicaspet.com.br/sitemap.xml
 * Envie esse URL no Google Search Console > Sitemaps.
 * ============================================================
 */

define('APP_ROOT', __DIR__);

require_once __DIR__ . '/config/functions.php';
require_once __DIR__ . '/models/Blog.php';

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl  = 'https://curicaspet.com.br';
$today    = date('Y-m-d');

// ── Páginas estáticas + âncoras de serviço ───────────────────────
$staticPages = [
    ['loc' => $baseUrl . '/',            'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => $baseUrl . '/orcamento',   'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/adocao',      'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/blog',        'priority' => '0.8', 'changefreq' => 'weekly'],
    ['loc' => $baseUrl . '/galeria',     'priority' => '0.5', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/#dog-walker',       'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/#hospedagem-hotel', 'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/#asilo-canino',     'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/#creche-daycare',   'priority' => '0.8', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/#pet-sitter',       'priority' => '0.8', 'changefreq' => 'monthly'],
];

// ── Artigos do blog (dinâmicos) ──────────────────────────────────
$blogModel = new Blog();
$artigos   = $blogModel->listarAtivos();

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

<?php foreach ($staticPages as $page): ?>
    <url>
        <loc><?= htmlspecialchars($page['loc'], ENT_XML1) ?></loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq><?= $page['changefreq'] ?></changefreq>
        <priority><?= $page['priority'] ?></priority>
    </url>
<?php endforeach; ?>

<?php if (!empty($artigos)): ?>
<?php foreach ($artigos as $artigo): ?>
    <url>
        <loc><?= htmlspecialchars($baseUrl . '/blog/' . $artigo['slug'], ENT_XML1) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($artigo['created_at'] ?? $today)) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
<?php endforeach; ?>
<?php endif; ?>

</urlset>