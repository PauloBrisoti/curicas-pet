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

$pageTitle       = 'Curicas Pet — Dog Walker com GPS e Hotel Pet em Osasco | Zona Oeste SP';
$pageDescription = 'Dog Walker com GPS em tempo real e Hotel Pet em Osasco, Pinheiros, Lapa, Boaçava e região. +527 pets felizes! Solicite seu orçamento pelo WhatsApp.';

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
                <!-- Badges de prova social -->
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <span class="badge fw-normal px-3 py-2"
                          style="background:rgba(143,220,79,.15); color:#8FDC4F; border:1px solid rgba(143,220,79,.3);">
                        ⭐ 5.0 no Google
                    </span>
                    <span class="badge bg-danger fw-normal px-3 py-2">
                        🔥 Vagas limitadas esta semana
                    </span>
                </div>

                <h1 class="display-5 fw-bold text-white mb-3 lh-sm">
                    Passeios com <span style="color:#8FDC4F;">GPS em Tempo Real</span>
                    em Osasco 🐾
                </h1>

                <ul class="list-unstyled mb-4" style="color:rgba(255,255,255,.75); font-size:1.05rem;">
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill me-2" style="color:#8FDC4F;"></i>
                        Acompanhe cada passo pelo celular
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill me-2" style="color:#8FDC4F;"></i>
                        Fotos e vídeos diários no WhatsApp
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill me-2" style="color:#8FDC4F;"></i>
                        <strong style="color:#fff;">527 pets felizes</strong> em Osasco e Zona Oeste
                    </li>
                </ul>

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

                <div class="d-flex gap-4" style="color:rgba(255,255,255,.5); font-size:.85rem;">
                    <span><i class="bi bi-shield-check me-1" style="color:#8FDC4F;"></i>Equipe certificada</span>
                    <span><i class="bi bi-clock me-1" style="color:#8FDC4F;"></i>Resposta em até 2h</span>
                </div>
            </div>

            <div class="col-lg-6 text-center animate-fade-in delay-2">
                <img src="assets/images/hero-dog.webp"
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
                $nomeSlug = rawurlencode($servico['nome']);

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

                // WhatsApp CTA
                $waMensagem  = rawurlencode('Olá, equipe Curicas Pet! Gostaria de um orçamento com desconto especial para o serviço de ' . $servico['nome'] . '.');
                $waUrl       = $whatsappBase . '?text=' . $waMensagem;
                $orcamentoUrl = !$ocultarPreco ? '/orcamento' : $waUrl;

                // Mapeamento de âncoras e URLs por serviço
                $servicoUrlMap = [
                    'dog-walker'       => '/#dog-walker',
                    'walking'          => '/#dog-walker',
                    'hospedagem-hotel' => '/#hospedagem-hotel',
                    'hotel'            => '/#hospedagem-hotel',
                    'asilo-canino'     => '/#asilo-canino',
                    'creche-daycare'   => '/#creche-daycare',
                    'pet-sitter'       => '/#pet-sitter',
                ];
                $saibaMaisUrl = $servicoUrlMap[$servicoDataAttr] ?? '/orcamento';
                // Serviço de Adoção aponta para /adocao
                if (stripos($servico['nome'], 'Ado') !== false) {
                    $saibaMaisUrl = '/adocao';
                }
            ?>
            <div id="<?= safe($servicoDataAttr) ?>"
                 class="row align-items-center g-4 g-lg-5 mb-5 pb-4
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

                    <!-- Preço ou mensagem de contato -->
                    <?php if ($ocultarPreco): ?>
                        <p class="text-muted fst-italic small mb-3 p-3 rounded-3"
                           style="background:#f8f9fa; border-left:3px solid #6BBF2D;">
                            Entre em contato para mais informações e orçamentos com descontos especiais.
                        </p>
                        <a href="<?= safe($waUrl) ?>" target="_blank" rel="noopener"
                           class="btn btn-success btn-lg px-4">
                            <i class="fab fa-whatsapp me-2"></i>Fazer Orçamento
                        </a>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div>
                                <p class="text-muted small mb-0">A partir de</p>
                                <p class="fw-bold mb-0" style="font-size:1.5rem; color:#4A9B2E;">
                                    R$ <?= number_format($servico['preco_base'], 0, ',', '.') ?>
                                    <small class="text-muted fw-normal" style="font-size:.9rem;"><?= $tipoCobranca ?></small>
                                </p>
                            </div>
                            <a href="<?= safe($saibaMaisUrl) ?>"
                               class="btn btn-outline-primary btn-lg px-4"
                               aria-label="Saiba mais sobre <?= safe($servico['nome']) ?>">
                                Saiba Mais
                            </a>
                            <a href="/orcamento"
                               class="btn btn-primary btn-lg px-4"
                               aria-label="Contratar <?= safe($servico['nome']) ?> — Solicitar orçamento">
                                Contratar &rarr;
                            </a>
                        </div>
                    <?php endif; ?>
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

        <div id="carrosselGaleria" class="carousel slide" data-bs-ride="carousel">
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
                                    allowfullscreen></iframe>
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
     NÚMEROS / PROVA SOCIAL
     ═══════════════════════════════════════════ -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Por que 527 Tutores Confiam na Curicas Pet?</h2>
            <p class="text-muted">Resultados que fazem a diferença em Osasco e Zona Oeste</p>
        </div>

        <div class="row g-4 mb-5">
            <?php
            $stats = [
                ['icon' => '🏆', 'value' => '100%',  'label' => 'Equipe Certificada',   'desc' => 'Curso de comportamento animal e primeiros socorros pet'],
                ['icon' => '📍', 'value' => '1.247', 'label' => 'Passeios em 2024',     'desc' => 'Todos com GPS em tempo real e foto no WhatsApp'],
                ['icon' => '⏱️', 'value' => '<2h',   'label' => 'Tempo de Resposta',    'desc' => 'Atendimento ágil pelo WhatsApp, inclusive fins de semana'],
            ];
            foreach ($stats as $idx => $s):
            ?>
            <div class="col-md-4 animate-fade-in delay-<?= $idx + 1 ?>">
                <div class="text-center p-4 rounded-4 h-100" style="background:#F8F9FA;">
                    <div class="mb-3" style="font-size:2.5rem;"><?= $s['icon'] ?></div>
                    <h3 class="fw-bold" style="color:#4A9B2E; font-size:2rem;"><?= $s['value'] ?></h3>
                    <h4 class="fw-bold fs-6"><?= $s['label'] ?></h4>
                    <p class="text-muted small"><?= $s['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Depoimentos -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h5 class="text-center mb-4 fw-semibold">
                    <i class="bi bi-chat-quote me-2" style="color:#6BBF2D;"></i>O que nossos clientes dizem
                </h5>
                <div class="row g-3">
                    <?php
                    $depoimentos = [
                        ['inicial' => 'C', 'nome' => 'Carla M.', 'color' => '#6BBF2D', 'texto' => '"Meu Thor ama os passeios! Recebo fotos todos os dias. Super recomendo o Dog Walker em Osasco!"', 'tempo' => 'Cliente há 8 meses'],
                        ['inicial' => 'R', 'nome' => 'Roberto S.', 'color' => '#4A9B2E', 'texto' => '"Deixei minha Luna no hotel por 10 dias. Voltou feliz e cheirosa! Melhor Hotel Pet da Zona Oeste."', 'tempo' => 'Cliente há 1 ano'],
                        ['inicial' => 'A', 'nome' => 'Ana Paula', 'color' => '#2D7A1F', 'texto' => '"Atendimento impecável! Sempre respondem rápido no WhatsApp e o Pet Sitter foi incrível!"', 'tempo' => 'Cliente há 6 meses'],
                    ];
                    foreach ($depoimentos as $dep):
                    ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 p-3">
                            <div class="d-flex align-items-center mb-2 gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                     style="width:44px;height:44px;background:<?= $dep['color'] ?>;font-size:1.1rem;">
                                    <?= $dep['inicial'] ?>
                                </div>
                                <div>
                                    <strong><?= $dep['nome'] ?></strong>
                                    <div style="color:#f59e0b; font-size:.85rem;">⭐⭐⭐⭐⭐</div>
                                </div>
                            </div>
                            <p class="small text-muted mb-1"><?= $dep['texto'] ?></p>
                            <small class="text-muted"><?= $dep['tempo'] ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     INSTAGRAM CTA — Fundo neutro/escuro (não gradiente pesado)
     ═══════════════════════════════════════════ -->
<section class="py-5" style="background:#1a2a0a;">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge mb-3 px-3 py-2"
                      style="background:rgba(143,220,79,.15); color:#8FDC4F;">
                    <i class="fab fa-instagram me-1"></i> @curicaspet
                </span>
                <h2 class="display-6 fw-bold text-white mb-3">
                    Acompanhe no Instagram!
                </h2>
                <p class="mb-4" style="color:rgba(255,255,255,.65);">
                    Veja fotos e vídeos diários dos passeios, bastidores do hotel e dicas de cuidados com seu pet
                    em Osasco e Zona Oeste SP.
                </p>
                <ul class="list-unstyled mb-4" style="color:rgba(255,255,255,.7);">
                    <li class="mb-2"><i class="bi bi-camera-video me-2" style="color:#8FDC4F;"></i>Reels dos passeios com música</li>
                    <li class="mb-2"><i class="bi bi-image me-2" style="color:#8FDC4F;"></i>Fotos dos pets no Hotel</li>
                    <li class="mb-2"><i class="bi bi-lightbulb me-2" style="color:#8FDC4F;"></i>Dicas de adestramento</li>
                </ul>
                <a href="https://www.instagram.com/curicaspet" target="_blank" rel="noopener"
                   class="btn btn-light btn-lg px-5 fw-bold">
                    <i class="fab fa-instagram me-2" style="color:#e6683c;"></i>Seguir @curicaspet
                </a>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <!-- Mini mockup de celular -->
                <div class="d-inline-block"
                     style="background:#0a1a05; border-radius:28px; padding:10px; box-shadow:0 20px 60px rgba(0,0,0,.5); max-width:240px;">
                    <div style="background:#fff; border-radius:20px; overflow:hidden;">
                        <div class="d-flex align-items-center gap-2 p-2 border-bottom">
                            <img src="assets/images/logo-curicas-pet.png" width="32" height="32"
                                 class="rounded-circle" alt="Curicas Pet">
                            <span class="fw-bold small">curicaspet</span>
                            <span class="ms-auto small" style="color:#6BBF2D;">Seguir</span>
                        </div>
                        <img src="assets/images/hero-dog.png?v=3"
                             alt="Curicas Pet no Instagram"
                             style="width:100%; height:180px; object-fit:cover;" loading="lazy">
                        <div class="p-2">
                            <div class="d-flex gap-3 mb-1" style="font-size:1.2rem; color:#333;">
                                <i class="bi bi-heart"></i>
                                <i class="bi bi-chat"></i>
                                <i class="bi bi-send"></i>
                            </div>
                            <small class="fw-bold">527 curtidas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     CTA FINAL — Fundo escuro (sem verde limão maciço)
     ═══════════════════════════════════════════ -->
<section class="py-5" style="background:linear-gradient(135deg,#2D3E1F,#1a2a0a);">
    <div class="container text-center text-white py-3">
        <div class="mb-3">
            <span class="badge bg-danger px-3 py-2" style="font-size:.9rem;">
                ⏰ OFERTA EXPIRA EM 48H
            </span>
        </div>
        <h2 class="display-6 fw-bold mb-3">🎁 20% OFF na Primeira Diária!</h2>
        <p class="mb-2" style="color:rgba(255,255,255,.85);">
            Apenas <strong>5 vagas restantes</strong> para novos clientes esta semana.
        </p>
        <p class="mb-4" style="color:rgba(255,255,255,.55);">Resposta em até 2 horas pelo WhatsApp.</p>
        <a href="<?= $whatsappBase . '?text=' . rawurlencode('Olá! Vi o desconto de 20% na primeira diária e gostaria de aproveitar!') ?>"
           target="_blank" rel="noopener"
           class="btn btn-success btn-lg px-5 fw-bold">
            <i class="fab fa-whatsapp me-2"></i>Quero Meu Desconto AGORA
        </a>
        <p class="mt-3 mb-0 small" style="color:rgba(255,255,255,.4);">
            <i class="bi bi-shield-check me-1"></i>Sem compromisso. Respondemos em até 2h.
        </p>
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