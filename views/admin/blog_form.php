<?php
/**
 * ============================================================
 * VIEW: ADMIN - BLOG FORM (CRIAR/EDITAR)
 * ============================================================
 */
$pageTitle = ($post['id'] ?? null) ? 'Editar Artigo' : 'Novo Artigo';
$isAdmin = true;

ob_start();
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-journal-plus me-2"></i>
                <?= $pageTitle ?>
            </h1>
            <p class="text-muted mb-0">
                <?= ($post['id'] ?? null) ? 'Edite as informações do artigo' : 'Preencha os dados do novo artigo' ?>
            </p>
        </div>
        <a href="index.php?page=admin&action=blog" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- Formulário -->
    <form action="index.php?page=admin&action=blog_salvar" method="POST" id="formBlog">
        <input type="hidden" name="id" value="<?= $post['id'] ?? '' ?>">

        <div class="row">
            <div class="col-lg-8">
                <!-- Conteúdo Principal -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="mb-0">Conteúdo</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título *</label>
                            <input type="text" class="form-control form-control-lg" id="titulo" name="titulo"
                                value="<?= safe($post['titulo'] ?? '') ?>" required
                                placeholder="Ex: Como Escolher um Dog Walker de Confiança">
                        </div>

                        <div class="mb-3">
                            <label for="resumo" class="form-label">Resumo</label>
                            <textarea class="form-control" id="resumo" name="resumo" rows="3"
                                placeholder="Breve descrição do artigo (aparece na listagem)"><?= safe($post['resumo'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="conteudo" class="form-label">Conteúdo *</label>
                            <textarea class="form-control" id="conteudo" name="conteudo" rows="15" required
                                placeholder="Escreva o conteúdo do artigo em HTML..."><?= safe($post['conteudo'] ?? '') ?></textarea>
                            <div class="form-text">
                                Use tags HTML: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Configurações -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="mb-0">Publicação</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="ativo" class="form-label">Status</label>
                            <select class="form-select" id="ativo" name="ativo">
                                <option value="1" <?= ($post['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Publicado</option>
                                <option value="0" <?= ($post['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Rascunho</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg me-2"></i>
                            <?= ($post['id'] ?? null) ? 'Salvar Alterações' : 'Publicar Artigo' ?>
                        </button>
                    </div>
                </div>

                <!-- SEO -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="mb-0">SEO</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                maxlength="160"
                                placeholder="Descrição para o Google (máx 160 caracteres)"><?= safe($post['meta_description'] ?? '') ?></textarea>
                            <div class="form-text">
                                <span id="charCount">0</span>/160 caracteres
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagem_destaque" class="form-label">URL Imagem Destaque</label>
                            <input type="url" class="form-control" id="imagem_destaque" name="imagem_destaque"
                                value="<?= safe($post['imagem_destaque'] ?? '') ?>" placeholder="https://...">
                        </div>
                    </div>
                </div>

                <?php if ($post['id'] ?? null): ?>
                    <!-- Info -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                Criado em:
                                <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-eye me-1"></i>
                                Views:
                                <?= number_format($post['views'] ?? 0) ?>
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    // Contador de caracteres para meta description
    document.getElementById('meta_description').addEventListener('input', function () {
        document.getElementById('charCount').textContent = this.value.length;
    });
    // Inicializar contador
    document.getElementById('charCount').textContent = document.getElementById('meta_description').value.length;
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>