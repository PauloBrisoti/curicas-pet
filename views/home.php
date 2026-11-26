<?php
/**
 * ============================================================
 * VIEW: HOME / PÁGINA INICIAL — v3.0
 * ============================================================
 * - Hero: fundo escuro (sem verde maciço)
 * - Serviços: Layout Z-Pattern alternado + ocultar_preco
 * - Instagram CTA: fundo neutro
 * - CTA final: escuro, não verde limão
 * - Cluster de Blog dinâmico no final
 * - rawurlencode() em todos os links WhatsApp
 * ============================================================
 */

$businessConfig = require __DIR__ . '/../config/business.php';

// Galeria para carrossel (sem controller intermediário)
require_once __DIR__ . '/../models/Gallery.php';
$galleryModel   = new Gallery();
$carrosselItems = $galleryModel->listarCarrossel(10);

// $artigosCluster e $servicos vêm do HomeController
$artigosCluster = $artigosCluster ?? [];

$pageTitle       = 'Curicas Pet — Serviços para pets em Osasco e Zona Oeste SP';
$pageDescription = 'Passeios com GPS, creche, hospedagem, cuidados para cães idosos e Pet Sitter em Osasco e Zona Oeste SP. Solicite um orçamento.';

$whatsappBase = 'https://wa.me/' . $businessConfig['whatsapp_num'];

// Mapa de benefícios por tipo de serviço
$beneficiosMap = [
    'Dog Walker'   => ['Passeio de 60 minutos', 'GPS em tempo real', 'Foto pelo WhatsApp', 'Rotas seguras em Osasco e Zona Oeste'],
    'Hospedagem'   => ['Livre, sem gaiolas', 'Monitoramento 24h', 'Passeios inclusos', 'Fotos diárias pelo WhatsApp'],
    'Hotel'        => ['Livre, sem gaiolas', 'Monitoramento 24h', 'Passeios inclusos', 'Fotos diárias pelo WhatsApp'],
    'Pet Sitter'   => ['Cuidador em domicílio', 'Alimentação e remédios', 'Brincadeiras', 'Pet fica confortável em casa'],
    'Creche'       => ['Período integral', 'Socialização supervisionada', 'Brincadeiras diárias', 'Ambiente climatizado'],
    'Daycare'      => ['Período integral', 'Socialização supervisionada', 'Brincadeiras diárias', 'Ambiente climatizado'],
];

// Cores de placeholder por índice (fallback enquanto WebP não existir)
$placeholderColors = ['6BBF2D', '4A9B2E', '8FDC4F', '2D7A1F', '5CA832'];

// Mapeamento: palavra no nome do serviço → slug do arquivo WebP local
// As imagens devem estar em: /assets/images/servicos/{slug}.webp
$servicoWebP = [
    'Asilo'      => 'asilo-canino',
    'Creche'     => 'creche-daycare',
    'Day Care'   => 'creche-daycare',
    'Daycare'    => 'creche-daycare',
    'Dog Walker' => 'dog-walker',
    'Walking'    => 'dog-walker',
    'Hospedagem' => 'hospedagem-hotel',
    'Hotel'      => 'hospedagem-hotel',
    'Pet Sitter' => 'pet-sitter',
    'Sitter'     => 'pet-sitter',
];

// Alt-texts otimizados por serviço (SEO Local Osasco)
$servicoAlt = [
    'asilo-canino'     => 'Cão idoso descansando no Asilo Canino da Curicas Pet em Osasco',
    'creche-daycare'   => 'Cães brincando livremente na Creche Canina da Curicas Pet em Osasco e Zona Oeste SP',
    'dog-walker'       => 'Dog Walker com GPS acompanhando cão em passeio seguro em Osasco',
    'hospedagem-hotel' => 'Cão hospedado de forma livre e acolhedora no Hotel Pet Curicas em Osasco',
    'pet-sitter'       => 'Pet Sitter profissional da Curicas Pet cuidando de cão em domicílio em Osasco',
];

ob_start();
?>

<!-- ═══════════════════════════════════════════
     HERO SECTION — Fundo escuro, sem verde maciço
     ═══════════════════════════════════════════ -->
<section class="hero-section py-5"
         style="background: linear-gradient(135deg, #1a2a0a 0%, #2D3E1F 100%); min-height: 520px; display:flex; align-items:center;">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 animate-fade-in">
                <h1 class="display-5 fw-bold text-white mb-3 lh-sm">
                    Cuidado para seu pet em Osasco e Zona Oeste
                </h1>
                <p class="lead text-white mb-4">
                    Passeios com GPS, creche, hospedagem, cuidados para cães idosos e visitas em domicílio.
                </p>

                <div class="d-flex gap-3 flex-wrap mb-3">
                    <a href="<?= $whatsappBase . '?text=' . rawurlencode('Olá! Gostaria de solicitar um orçamento.') ?>"
                       target="_blank" rel="noopener"
                       class="btn btn-success btn-lg px-4 fw-bold">
                        <i class="fab fa-whatsapp me-2"></i>Solicitar Orçamento
                    </a>
                    <a href="#servicos" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-arrow-down me-2"></i>Ver Serviços
                    </a>
                </div>

            </div>

            <div class="col-lg-6 text-center animate-fade-in delay-2">
                <img src="/assets/images/hero-dog.webp"
                     alt="Cachorro feliz no passeio com Dog Walker em Osasco"
                     class="img-fluid rounded-4 shadow-lg"
                     width="800" height="800"
                     fetchpriority="high"
                     decoding="async"
                     style="max-height:420px; object-fit:cover;">
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     SERVIÇOS — Z-Pattern Alternado
     ═══════════════════════════════════════════ -->
<section id="servicos" class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge text-uppercase fw-semibold mb-2 px-3 py-2"
                  style="background:rgba(107,191,45,.12); color:#2D6A0A; border-radius:50px; letter-spacing:.05em;">
                Nossos Serviços
            </span>
            <h2 class="display-6 fw-bold">O Cuidado que Seu Pet Merece</h2>
            <p class="text-muted">Atendemos Osasco, Pinheiros, Lapa, Boaçava e Zona Oeste SP</p>
        </div>

        <?php if (empty($servicos)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-grid display-4"></i>
                <p class="mt-3">Serviços em breve. Entre em contato pelo WhatsApp!</p>
            </div>
        <?php else: ?>
            <?php foreach ($servicos as $i => $servico):
                $ocultarPreco  = !empty($servico['ocultar_preco']);
                $isImpar       = ($i % 2 !== 0); // alterna direção do Z-Pattern
                $beneficios    = $beneficiosMap[$servico['nome']] ?? [
                    'Atendimento personalizado',
                    'Profissionais qualificados',
                    'Ambiente seguro',
                    'Suporte WhatsApp'
                ];
                $tipoCobranca  = match ($servico['tipo_calculo']) {
                    'diaria'  => '/diária',
                    'unidade' => '/passeio',
                    'hora'    => '/hora',
                    default   => ''
                };
                // ── Resolução de imagem ───────────────────────────────
                // Prioridade: imagem salva no admin → WebP local → placeholder
                $cor      = $placeholderColors[$i % count($placeholderColors)];

                // Encontrar slug WebP pelo nome do serviço
                $webpKey = null;
                foreach ($servicoWebP as $keyword => $arquivo) {
                    if (stripos($servico['nome'], $keyword) !== false) {
                        $webpKey = $arquivo;
                        break;
                    }
                }

                // Montar src: admin > WebP local > via.placeholder.com
                if (!empty($servico['imagem_url'])) {
                    $imagemSrc = $servico['imagem_url'];
                } elseif ($webpKey) {
                    // Arquivo subido pelo usuário — usar direto (sem file_exists)
                    $imagemSrc = '/assets/images/servicos/' . $webpKey . '.webp';
                } else {
                    $imagemSrc = 'https://via.placeholder.com/600x420/' . $cor . '/ffffff?text=' . $nomeSlug;
                }

                // Alt SEO ou genérico
                $altImagem = ($webpKey && isset($servicoAlt[$webpKey]))
                    ? $servicoAlt[$webpKey]
                    : safe($servico['nome']) . ' em Osasco e Zona Oeste SP';

                // Slug para data-service GTM
                $servicoDataAttr = $webpKey
                    ?? strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($servico['nome'])));

                $orcamentoUrl = '/orcamento?servico=' . rawurlencode((string) $servico['id']);
            ?>
            <div id="<?= safe($servicoDataAttr) ?>"
                 class="service-anchor row align-items-center g-4 g-lg-5 mb-5 pb-4
                        <?= $i > 0 ? 'border-top pt-5' : '' ?>"
                 style="border-color:#f0f4ec !important;"
                 data-service="<?= safe($servicoDataAttr) ?>">

                <!-- Imagem -->
                <div class="col-lg-5 <?= $isImpar ? 'order-lg-2' : '' ?> animate-fade-in">
                    <div class="rounded-4 overflow-hidden shadow-sm"
                         style="aspect-ratio:4/3;">
                        <img src="<?= safe($imagemSrc) ?>"
                             alt="<?= $altImagem ?>"
                             class="img-fluid w-100 h-100"
                             style="object-fit:cover;"
                             width="600" height="450"
                             loading="lazy"
                             decoding="async">
                    </div>
                </div>

                <!-- Texto -->
                <div class="col-lg-7 <?= $isImpar ? 'order-lg-1' : '' ?> animate-fade-in delay-1">
                    <!-- Badge Bootstrap Icon -->
                    <div class="d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:56px;height:56px;background:linear-gradient(135deg,#8FDC4F,#4A9B2E);border-radius:16px;color:#fff;font-size:1.5rem;">
                        <i class="bi <?= safe($servico['icone_bootstrap']) ?>"></i>
                    </div>

                    <h2 class="h3 fw-bold mb-2"><?= safe($servico['nome']) ?></h2>
                    <div class="text-muted mb-3 servico-descricao"><?= safeHtml($servico['descricao']) ?></div>

                    <!-- Bullets de benefícios -->
                    <ul class="list-unstyled mb-4">
                        <?php foreach ($beneficios as $b): ?>
                        <li class="mb-1 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill" style="color:#6BBF2D;font-size:.9rem;flex-shrink:0;"></i>
                            <span class="small"><?= safe($b) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <?php if (!$ocultarPreco): ?>
                            <div>
                                <p class="text-muted small mb-0">A partir de</p>
                                <p class="fw-bold mb-0" style="font-size:1.5rem; color:#4A9B2E;">
                                    R$ <?= number_format((float) $servico['preco_base'], 2, ',', '.') ?>
                                    <small class="text-muted fw-normal" style="font-size:.9rem;"><?= $tipoCobranca ?></small>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if (stripos($servico['nome'], 'Ado') !== false): ?>
                            <a href="/adocao" class="btn btn-primary px-4">Ver pets para adoção</a>
                        <?php else: ?>
                            <a href="<?= safe($orcamentoUrl) ?>" class="btn btn-primary px-4"
                               aria-label="Pedir orçamento para <?= safe($servico['nome']) ?>">
                                Pedir orçamento
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     CARROSSEL DE GALERIA
     ═══════════════════════════════════════════ -->
<?php if (!empty($carrosselItems)): ?>
<section class="py-5" style="background:#F8F9FA;">
    <div class="container py-3">
        <div class="text-center mb-4">
            <h2 class="display-6 fw-bold">Nosso Dia a Dia</h2>
            <p class="text-muted">Veja como cuidamos dos pets com amor e dedicação</p>
        </div>

        <div id="carrosselGaleria" class="carousel slide">
            <div class="carousel-indicators">
                <?php foreach ($carrosselItems as $idx => $item): ?>
                <button type="button" data-bs-target="#carrosselGaleria"
                        data-bs-slide-to="<?= $idx ?>"
                        <?= $idx === 0 ? 'class="active" aria-current="true"' : '' ?>
                        aria-label="Slide <?= $idx + 1 ?>"></button>
                <?php endforeach; ?>
            </div>

            <div class="carousel-inner rounded-4 shadow overflow-hidden">
                <?php foreach ($carrosselItems as $idx => $item): ?>
                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                    <?php if (($item['tipo'] ?? 'foto') === 'video'): ?>
                        <div class="ratio ratio-16x9" style="max-height:500px;">
                            <iframe src="<?= safe($item['imagem']) ?>"
                                    title="<?= safe($item['titulo']) ?>"
                                    loading="lazy" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <img src="<?= safe($item['imagem']) ?>"
                             class="d-block w-100"
                             alt="<?= safe($item['titulo']) ?>"
                             style="height:460px; object-fit:cover;" loading="lazy">
                    <?php endif; ?>

                    <div class="carousel-caption d-none d-md-block"
                         style="background:linear-gradient(transparent, rgba(0,0,0,.55)); border-radius:0 0 1rem 1rem; padding:1.5rem;">
                        <h5 class="fw-bold"><?= safe($item['titulo']) ?></h5>
                        <?php if (!empty($item['descricao'])): ?>
                            <p class="small"><?= safe($item['descricao']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carrosselGaleria" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carrosselGaleria" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════
     INSTAGRAM CTA — Fundo neutro/escuro (não gradiente pesado)
     ═══════════════════════════════════════════ -->
<section class="py-5" style="background:#1a2a0a;">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-8 mx-auto text-center">
                <span class="badge mb-3 px-3 py-2"
                      style="background:rgba(143,220,79,.15); color:#8FDC4F;">
                    <i class="fab fa-instagram me-1"></i> @curicaspet
                </span>
                <h2 class="display-6 fw-bold text-white mb-3">
                    Acompanhe no Instagram!
                </h2>
                <p class="mb-4" style="color:rgba(255,255,255,.65);">
                    Veja fotos e vídeos dos passeios, bastidores do hotel e dicas de cuidados com seu pet
                    em Osasco e Zona Oeste SP.
                </p>
                <a href="https://www.instagram.com/curicaspet" target="_blank" rel="noopener"
                   class="btn btn-light btn-lg px-5 fw-bold">
                    <i class="fab fa-instagram me-2" style="color:#e6683c;"></i>Seguir @curicaspet
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     CTA FINAL — Fundo escuro (sem verde limão maciço)
     ═══════════════════════════════════════════ -->
<section class="py-5" style="background:linear-gradient(135deg,#2D3E1F,#1a2a0a);">
    <div class="container text-center text-white py-3">
        <h2 class="display-6 fw-bold mb-3">Converse com a equipe sobre o cuidado do seu pet</h2>
        <a href="/orcamento" class="btn btn-success btn-lg px-4 fw-bold">
            Solicitar orçamento
        </a>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     CLUSTER DE BLOG — Conteúdos Recomendados
     ═══════════════════════════════════════════ -->
<?php if (!empty($artigosCluster)): ?>
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="badge mb-2 px-3 py-2"
                      style="background:rgba(107,191,45,.12); color:#4A9B2E; border-radius:50px;">
                    Blog
                </span>
                <h2 class="fw-bold mb-0">Conteúdos Recomendados</h2>
                <p class="text-muted small mt-1">Dicas para tutores em Osasco e Zona Oeste</p>
            </div>
            <a href="/blog" class="btn btn-outline-primary btn-sm px-4">
                Ver todos os artigos →
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($artigosCluster as $artigo): ?>
            <div class="col-md-4">
                <a href="/blog/<?= safe($artigo['slug']) ?>" class="text-decoration-none text-dark">
                    <article class="card border-0 shadow-sm h-100">
                        <?php
                        $imgArtigo = !empty($artigo['imagem_destaque'])
                            ? $artigo['imagem_destaque']
                            : 'https://via.placeholder.com/400x220/6BBF2D/ffffff?text=' . rawurlencode($artigo['titulo']);
                        ?>
                        <img src="<?= safe($imgArtigo) ?>"
                             alt="<?= safe($artigo['titulo']) ?>"
                             class="card-img-top"
                             style="height:180px; object-fit:cover;"
                             loading="lazy">
                        <div class="card-body">
                            <span class="badge mb-2"
                                  style="background:rgba(107,191,45,.15); color:#4A9B2E; font-size:.75rem;">
                                <?= safe($artigo['categoria'] ?? 'Dicas') ?>
                            </span>
                            <h3 class="card-title h6 fw-bold"><?= safe($artigo['titulo']) ?></h3>
                            <p class="card-text text-muted small">
                                <?= safe(mb_substr($artigo['resumo'] ?? '', 0, 100)) ?>...
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                <?= date('d/m/Y', strtotime($artigo['created_at'])) ?>
                                <span class="ms-2">
                                    <i class="bi bi-eye me-1"></i><?= number_format($artigo['views'] ?? 0) ?>
                                </span>
                            </small>
                        </div>
                    </article>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$content = ob_get_clean();

require __DIR__ . '/layouts/base.php';
?>