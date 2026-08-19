<?php
/**
 * ============================================================
 * VIEW: BLOG - ARTIGO INDIVIDUAL
 * ============================================================
 */

// SEO dinâmico baseado no post
$pageTitle = $post['titulo'] . ' | Blog Curicas Pet';
$pageDescription = $post['meta_description'] ?? mb_substr(strip_tags($post['resumo'] ?? ''), 0, 160);

ob_start();
?>

<!-- Breadcrumb -->
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                <li class="breadcrumb-item"><a href="index.php?page=blog">Blog</a></li>
                <li class="breadcrumb-item active">
                    <?= safe(mb_substr($post['titulo'], 0, 40)) ?>...
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Artigo -->
<article class="py-5">
    <div class="container">
        <div class="row">
            <!-- Conteúdo Principal -->
            <div class="col-lg-8">
                <header class="mb-4">
                    <h1 class="display-6 fw-bold mb-3">
                        <?= safe($post['titulo']) ?>
                    </h1>

                    <div class="d-flex gap-3 text-muted mb-4">
                        <span>
                            <i class="bi bi-calendar me-1"></i>
                            <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                        </span>
                        <span>
                            <i class="bi bi-eye me-1"></i>
                            <?= number_format($post['views'] ?? 0) ?> visualizações
                        </span>
                    </div>

                    <?php if (!empty($post['resumo'])): ?>
                        <p class="lead text-muted">
                            <?= safe($post['resumo']) ?>
                        </p>
                    <?php endif; ?>
                </header>

                <?php if (!empty($post['imagem_destaque'])): ?>
                    <img src="<?= safe($post['imagem_destaque']) ?>" alt="<?= safe($post['titulo']) ?>"
                        class="img-fluid rounded-4 mb-4 w-100" style="max-height: 400px; object-fit: cover;">
                <?php endif; ?>

                <!-- Conteúdo do Artigo -->
                <div class="article-content">
                    <?= $post['conteudo'] ?>
                </div>

                <!-- CTA no final -->
                <div class="bg-success text-white rounded-4 p-4 mt-5 text-center">
                    <h3 class="fw-bold mb-3">🐕 Gostou do artigo?</h3>
                    <p>Solicite um orçamento gratuito para nossos serviços!</p>
                    <a href="index.php?page=orcamento" class="btn btn-light btn-lg rounded-pill px-5">
                        <i class="bi bi-calculator me-2"></i>Orçamento Grátis
                    </a>
                </div>

                <!-- Compartilhar -->
                <div class="mt-4 pt-4 border-top">
                    <h5 class="mb-3">Compartilhar:</h5>
                    <div class="d-flex gap-2">
                        <a href="https://wa.me/?text=<?= urlencode($post['titulo'] . ' - ' . 'https://curicaspet.com.br/index.php?page=artigo&slug=' . $post['slug']) ?>"
                            target="_blank" class="btn btn-success rounded-circle" style="width: 45px; height: 45px;">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('https://curicaspet.com.br/index.php?page=artigo&slug=' . $post['slug']) ?>"
                            target="_blank" class="btn btn-primary rounded-circle" style="width: 45px; height: 45px;">
                            <i class="bi bi-facebook"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Posts Relacionados -->
                <?php if (!empty($postsRelacionados)): ?>
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-success text-white rounded-top-4">
                            <h3 class="h6 mb-0">
                                <i class="bi bi-journal-text me-2"></i>Outros Artigos
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($postsRelacionados as $rel): ?>
                                    <?php if ($rel['id'] !== $post['id']): ?>
                                        <li class="mb-3 pb-3 border-bottom">
                                            <a href="index.php?page=artigo&slug=<?= safe($rel['slug']) ?>"
                                                class="text-decoration-none text-dark">
                                                <strong>
                                                    <?= safe($rel['titulo']) ?>
                                                </strong>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- CTA Sidebar -->
                <div class="card border-0 shadow-sm rounded-4 bg-light sticky-top" style="top: 100px;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="font-size: 3rem;">📞</div>
                        <h4 class="fw-bold">Fale Conosco</h4>
                        <p class="text-muted small">Atendimento rápido pelo WhatsApp</p>
                        <a href="https://wa.me/5511969440485?text=Olá! Vi o blog e gostaria de saber mais sobre os serviços."
                            target="_blank" class="btn btn-success rounded-pill w-100">
                            <i class="bi bi-whatsapp me-2"></i>WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<style>
    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
    }

    .article-content h2 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
        color: #1e293b;
    }

    .article-content h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .article-content p {
        margin-bottom: 1.25rem;
    }

    .article-content ul,
    .article-content ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }

    .article-content li {
        margin-bottom: 0.5rem;
    }

    .article-content table {
        width: 100%;
        margin: 1.5rem 0;
        border-collapse: collapse;
    }

    .article-content th,
    .article-content td {
        border: 1px solid #dee2e6;
        padding: 0.75rem;
        text-align: left;
    }

    .article-content th {
        background: #f8f9fa;
    }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/base.php';
?>