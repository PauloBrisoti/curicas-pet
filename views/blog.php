<?php
/**
 * ============================================================
 * VIEW: BLOG — v3.0
 * ============================================================
 * - Cards modernos com thumbnail 200px, border-radius:16px
 * - Sem headers coloridos (verde maciço removido)
 * - Hero com fundo escuro consistente
 * - Links via Clean URLs /blog/slug
 * - Sidebar mantida, com visual neutro
 * ============================================================
 */

$pageTitle       = 'Blog — Dicas e Artigos sobre Pets | Curicas Pet Osasco';
$pageDescription = 'Dicas, artigos e guias sobre cuidados com cães e gatos. Dog Walker, Hotel Pet, adestramento e bem-estar animal em Osasco e Zona Oeste SP.';

ob_start();
?>

<!-- ── Hero ── -->
<section class="py-5" style="background:linear-gradient(135deg,#1a2a0a,#2D3E1F);">
    <div class="container py-3 text-center text-white">
        <span class="badge px-3 py-2 mb-3"
              style="background:rgba(143,220,79,.15); color:#8FDC4F;">
            <i class="bi bi-journal-richtext me-1"></i> Conteúdos sobre Pets
        </span>
        <h1 class="display-5 fw-bold mb-2">Blog Curicas Pet</h1>
        <p class="lead mb-0" style="color:rgba(255,255,255,.7);">
            Dicas, guias e artigos para tutores em Osasco e Zona Oeste SP
        </p>
    </div>
</section>

<!-- ── Listagem ── -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <!-- Posts Principais -->
            <div class="col-lg-8">
                <?php if (empty($posts)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x display-4 text-muted"></i>
                        <h4 class="mt-3 fw-semibold">Nenhum artigo publicado ainda</h4>
                        <p class="text-muted">Volte em breve! Estamos preparando conteúdos incríveis.</p>
                        <a href="/orcamento" class="btn btn-success mt-2">Solicitar Orçamento</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($posts as $post):

                            // ── Resolver imagem do post ──────────────────────────────
                            // imagem_destaque armazena URL completa (https://...) ou fica vazio.
                            // ATENÇÃO: não usar $slug aqui — conflita com a variável do post.
                            $imgRaw      = trim($post['imagem_destaque'] ?? '');
                            $imgFallback = '/assets/images/logo-curicas-pet.png';

                            if (empty($imgRaw)) {
                                $imgPost = $imgFallback;
                            } elseif (str_starts_with($imgRaw, 'http://') || str_starts_with($imgRaw, 'https://')) {
                                $imgPost = $imgRaw;             // URL absoluta → usar direto
                            } elseif (str_starts_with($imgRaw, '/')) {
                                $imgPost = $imgRaw;             // Path absoluto local → usar direto
                            } else {
                                // Nome de arquivo simples → montar path local (sem file_exists)
                                $imgPost = '/assets/images/blog/' . ltrim($imgRaw, '/');
                            }
                        ?>
                        <div class="col-md-6">
                            <article class="card h-100 border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">

                                <!-- Thumbnail -->
                                <a href="/blog/<?= safe($post['slug']) ?>" class="d-block overflow-hidden">
                                    <img src="<?= safe($imgPost) ?>"
                                         alt="<?= safe($post['titulo']) ?>"
                                         class="card-img-top"
                                         style="height:200px; object-fit:cover; transition:transform .4s;"
                                         loading="lazy"
                                         width="600" height="200"
                                         onerror="this.onerror=null; this.src='<?= safe($imgFallback) ?>';"
                                         onmouseover="this.style.transform='scale(1.04)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                </a>

                                <!-- Corpo -->
                                <div class="card-body d-flex flex-column p-3">
                                    <?php if (!empty($post['categoria'])): ?>
                                    <span class="badge mb-2 align-self-start"
                                          style="background:rgba(107,191,45,.15); color:#4A9B2E; font-size:.75rem;">
                                        <?= safe($post['categoria']) ?>
                                    </span>
                                    <?php endif; ?>

                                    <h2 class="card-title h6 fw-bold mb-2">
                                        <a href="/blog/<?= safe($post['slug']) ?>"
                                           class="text-dark text-decoration-none stretched-link">
                                            <?= safe($post['titulo']) ?>
                                        </a>
                                    </h2>

                                    <p class="card-text text-muted small flex-grow-1">
                                        <?= safe(mb_substr($post['resumo'] ?? '', 0, 110)) ?>...
                                    </p>
                                </div>

                                <!-- Footer do card -->
                                <div class="card-footer bg-white border-0 pt-0 px-3 pb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-eye me-1"></i>
                                            <?= number_format($post['views'] ?? 0) ?> views
                                        </small>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">

                <!-- Posts Populares -->
                <?php if (!empty($postsPopulares)): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-header border-0 fw-bold pb-0 pt-3 px-3"
                         style="background:transparent; color:#1a2a0a;">
                        <i class="bi bi-fire me-1" style="color:#ef4444;"></i> Mais Lidos
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($postsPopulares as $idx => $pop): ?>
                            <li class="<?= $idx < count($postsPopulares) - 1 ? 'mb-3 pb-3 border-bottom' : '' ?>">
                                <a href="/blog/<?= safe($pop['slug']) ?>"
                                   class="text-decoration-none text-dark fw-semibold small d-block mb-1">
                                    <?= safe($pop['titulo']) ?>
                                </a>
                                <small class="text-muted">
                                    <i class="bi bi-eye me-1"></i>
                                    <?= number_format($pop['views'] ?? 0) ?> views
                                </small>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <!-- CTA Orçamento -->
                <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
                    <div style="background:linear-gradient(135deg,#1a2a0a,#2D3E1F); padding:24px; text-align:center;">
                        <div style="font-size:2.5rem; margin-bottom:.5rem;">🐾</div>
                        <h4 class="fw-bold text-white mb-2">Precisa de Dog Walker?</h4>
                        <p class="mb-3" style="color:rgba(255,255,255,.7); font-size:.9rem;">
                            Respondemos em até 2 horas pelo WhatsApp!
                        </p>
                        <a href="/orcamento"
                           class="btn btn-success rounded-pill px-4 fw-semibold w-100">
                            <i class="bi bi-chat-dots me-2"></i>Solicitar Orçamento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/base.php';
?>