<?php
/**
 * ============================================================
 * VIEW: ADMIN - GALERIA FORM (UPLOAD FOTO/VIDEO)
 * ============================================================
 */
$pageTitle = ($foto['id'] ?? null) ? 'Editar Mídia' : 'Adicionar Mídia';
$isAdmin = true;

ob_start();
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-collection-play me-2"></i>
                <?= $pageTitle ?>
            </h1>
        </div>
        <a href="index.php?page=admin&action=gallery" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- Formulário -->
    <form action="index.php?page=admin&action=gallery_salvar" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $foto['id'] ?? '' ?>">

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <!-- Tipo: Foto ou Vídeo -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tipo de Mídia *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo" id="tipoFoto" value="foto"
                                    <?= ($foto['tipo'] ?? 'foto') === 'foto' ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success" for="tipoFoto">
                                    <i class="bi bi-image me-2"></i>Foto
                                </label>

                                <input type="radio" class="btn-check" name="tipo" id="tipoVideo" value="video"
                                    <?= ($foto['tipo'] ?? '') === 'video' ? 'checked' : '' ?>>
                                <label class="btn btn-outline-danger" for="tipoVideo">
                                    <i class="bi bi-play-btn me-2"></i>Vídeo
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título *</label>
                            <input type="text" class="form-control" id="titulo" name="titulo"
                                value="<?= safe($foto['titulo'] ?? '') ?>" required
                                placeholder="Ex: Passeio com Thor no parque">
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (opcional)</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="2"
                                placeholder="Descrição breve"><?= safe($foto['descricao'] ?? '') ?></textarea>
                        </div>

                        <!-- Upload de Foto -->
                        <div class="mb-3" id="campoFoto">
                            <label for="foto" class="form-label">
                                <?= ($foto['id'] ?? null) ? 'Nova Foto (opcional)' : 'Foto *' ?>
                            </label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">Formatos: JPG, PNG, WebP. Máximo 5MB.</div>
                        </div>

                        <!-- URL do Vídeo -->
                        <div class="mb-3" id="campoVideo" style="display: none;">
                            <label for="video_url" class="form-label">URL do Vídeo (YouTube ou Vimeo) *</label>
                            <input type="url" class="form-control" id="video_url" name="video_url"
                                value="<?= ($foto['tipo'] ?? '') === 'video' ? safe($foto['imagem'] ?? '') : '' ?>"
                                placeholder="https://www.youtube.com/watch?v=...">
                            <div class="form-text">Cole o link do YouTube ou Vimeo aqui.</div>
                        </div>

                        <?php if (!empty($foto['imagem'])): ?>
                            <div class="mb-3" id="previewAtual">
                                <label class="form-label">Mídia Atual</label>
                                <div>
                                    <?php if (($foto['tipo'] ?? 'foto') === 'video'): ?>
                                        <div class="ratio ratio-16x9" style="max-width: 400px;">
                                            <iframe src="<?= safe($foto['imagem']) ?>" allowfullscreen></iframe>
                                        </div>
                                    <?php else: ?>
                                        <img src="<?= safe($foto['imagem']) ?>" alt="<?= safe($foto['titulo'] ?? '') ?>"
                                            class="rounded-3" style="max-height: 200px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Ordem e Status -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ordem" class="form-label">Ordem no Carrossel</label>
                                    <input type="number" class="form-control" id="ordem" name="ordem"
                                        value="<?= $foto['ordem'] ?? 0 ?>" min="0">
                                    <div class="form-text">Menor número = aparece primeiro</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo"
                                            value="1" <?= ($foto['ativo'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="ativo">Exibir no carrossel</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="bi bi-check-lg me-2"></i>
                            <?= ($foto['id'] ?? null) ? 'Salvar Alterações' : 'Adicionar' ?>
                        </button>

                        <a href="index.php?page=admin&action=gallery" class="btn btn-outline-secondary w-100">
                            Cancelar
                        </a>
                    </div>
                </div>

                <!-- Dicas -->
                <div class="card border-0 shadow-sm rounded-4 mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2"></i>Dicas</h6>
                        <ul class="small text-muted mb-0">
                            <li>Use fotos de alta qualidade</li>
                            <li>Para vídeos, copie o link de "Compartilhar"</li>
                            <li>Ordene por relevância (menor = primeiro)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Toggle entre foto e vídeo
    document.querySelectorAll('input[name="tipo"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const campoFoto = document.getElementById('campoFoto');
            const campoVideo = document.getElementById('campoVideo');
            const fotoInput = document.getElementById('foto');
            const videoInput = document.getElementById('video_url');

            if (this.value === 'video') {
                campoFoto.style.display = 'none';
                campoVideo.style.display = 'block';
                fotoInput.removeAttribute('required');
            } else {
                campoFoto.style.display = 'block';
                campoVideo.style.display = 'none';
                <?php if (!($foto['id'] ?? null)): ?>
                    fotoInput.setAttribute('required', 'required');
                <?php endif; ?>
            }
        });
    });

    // Trigger inicial
    document.querySelector('input[name="tipo"]:checked')?.dispatchEvent(new Event('change'));
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>