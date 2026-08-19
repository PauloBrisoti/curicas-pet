<?php
/**
 * ============================================================
 * VIEW: GALERIA PÚBLICA
 * ============================================================
 */

$pageTitle = 'Galeria - Curicas Pet';
$pageDescription = 'Veja fotos dos nossos passeios e hospedagem. Cães felizes em Osasco, Pinheiros, Lapa e região.';

require_once __DIR__ . '/../models/Gallery.php';
$galleryModel = new Gallery();
$fotos = $galleryModel->listarAtivas(50);

ob_start();
?>

<!-- Hero -->
<section class="py-5" style="background: linear-gradient(135deg, #8FDC4F 0%, #6BBF2D 100%);">
    <div class="container py-4">
        <div class="text-center text-white">
            <h1 class="display-5 fw-bold mb-3">
                <i class="bi bi-images me-2"></i>Galeria
            </h1>
            <p class="lead mb-0">Momentos especiais dos nossos pets</p>
        </div>
    </div>
</section>

<!-- Galeria -->
<section class="py-5">
    <div class="container">
        <?php if (empty($fotos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-camera text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Em breve!</h4>
                <p class="text-muted">Estamos preparando fotos incríveis para você.</p>
                <a href="index.php" class="btn btn-success">
                    <i class="bi bi-house me-2"></i>Voltar ao Início
                </a>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($fotos as $foto): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="<?= safe($foto['imagem']) ?>" data-lightbox="galeria" data-title="<?= safe($foto['titulo']) ?>"
                            class="d-block gallery-item">
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
                                <img src="<?= safe($foto['imagem']) ?>" alt="<?= safe($foto['titulo']) ?>"
                                    class="card-img gallery-img" style="height: 200px; object-fit: cover;">
                                <div class="gallery-overlay">
                                    <i class="bi bi-zoom-in"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center">
            <h3 class="fw-bold mb-3">Quer ver seu pet aqui?</h3>
            <p class="text-muted mb-4">Contrate nossos serviços e receba fotos diárias pelo WhatsApp!</p>
            <a href="index.php?page=orcamento" class="btn btn-success btn-lg rounded-pill px-5">
                <i class="bi bi-calculator me-2"></i>Solicitar Orçamento
            </a>
        </div>
    </div>
</section>

<style>
    .gallery-item {
        position: relative;
        text-decoration: none;
    }

    .gallery-img {
        transition: transform 0.3s ease;
    }

    .gallery-item:hover .gallery-img {
        transform: scale(1.05);
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .gallery-overlay i {
        font-size: 2rem;
        color: white;
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }
</style>

<!-- Lightbox CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script>
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': 'Foto %1 de %2'
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/base.php';
?>