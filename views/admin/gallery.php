<?php
/**
 * ============================================================
 * VIEW: ADMIN - GALERIA (LISTAGEM)
 * ============================================================
 */
$pageTitle = 'Gerenciar Galeria - Admin';
$isAdmin = true;
require_once __DIR__ . '/../../models/Gallery.php';
$galleryModel = new Gallery();
$fotos = $galleryModel->listarTodas();

ob_start();
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-images me-2"></i>Galeria
            </h1>
            <p class="text-muted mb-0">Fotos dos passeios e hospedagem</p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=admin&action=dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
            <a href="index.php?page=admin&action=gallery_form" class="btn btn-success">
                <i class="bi bi-plus-lg me-2"></i>Adicionar Foto
            </a>
        </div>
    </div>

    <!-- Grid de Fotos -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <?php if (empty($fotos)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Nenhuma foto na galeria</h5>
                    <p class="text-muted">Adicione fotos dos passeios para mostrar no site</p>
                    <a href="index.php?page=admin&action=gallery_form" class="btn btn-success">
                        <i class="bi bi-plus-lg me-2"></i>Adicionar Foto
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden position-relative">
                                <?php if (($foto['tipo'] ?? 'foto') === 'video'): ?>
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-dark text-white"
                                        style="height: 150px;">
                                        <i class="bi bi-play-circle" style="font-size: 3rem;"></i>
                                    </div>
                                <?php else: ?>
                                    <img src="<?= safe($foto['imagem']) ?>" alt="<?= safe($foto['titulo']) ?>" class="card-img-top"
                                        style="height: 150px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <p class="small fw-bold mb-0 text-truncate flex-grow-1">
                                            <?= safe($foto['titulo']) ?>
                                        </p>
                                        <span class="badge bg-secondary ms-1">#<?= $foto['ordem'] ?? 0 ?></span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="index.php?page=admin&action=gallery_form&id=<?= $foto['id'] ?>"
                                            class="btn btn-sm btn-outline-secondary flex-fill">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger flex-fill"
                                            onclick="confirmarExclusao(<?= $foto['id'] ?>, '<?= safe($foto['titulo']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php if (!$foto['ativo']): ?>
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-secondary">Oculta</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (($foto['tipo'] ?? 'foto') === 'video'): ?>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-danger"><i class="bi bi-play-fill"></i></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Deseja realmente excluir a foto "<strong id="nomeFotoExcluir"></strong>"?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnConfirmarExclusao" class="btn btn-danger">
                    <i class="bi bi-trash me-2"></i>Excluir
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarExclusao(id, titulo) {
        document.getElementById('nomeFotoExcluir').textContent = titulo;
        document.getElementById('btnConfirmarExclusao').href = 'index.php?page=admin&action=gallery_excluir&id=' + id;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>