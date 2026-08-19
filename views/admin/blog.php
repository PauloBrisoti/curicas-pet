<?php
/**
 * ============================================================
 * VIEW: ADMIN - BLOG (LISTAGEM)
 * ============================================================
 */
$pageTitle = 'Gerenciar Blog - Admin';
$isAdmin = true;
require_once __DIR__ . '/../../models/Blog.php';
$blogModel = new Blog();
$posts = $blogModel->listarTodos();

ob_start();
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-journal-richtext me-2"></i>Blog
            </h1>
            <p class="text-muted mb-0">Gerencie os artigos do blog</p>
        </div>
        <a href="index.php?page=admin&action=blog_form" class="btn btn-success">
            <i class="bi bi-plus-lg me-2"></i>Novo Artigo
        </a>
    </div>

    <!-- Tabela de Posts -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <?php if (empty($posts)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Nenhum artigo cadastrado</h5>
                    <p class="text-muted">Crie seu primeiro artigo para o blog</p>
                    <a href="index.php?page=admin&action=blog_form" class="btn btn-success">
                        <i class="bi bi-plus-lg me-2"></i>Criar Artigo
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th class="text-center">Views</th>
                                <th class="text-center">Status</th>
                                <th>Data</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= safe($post['titulo']) ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">/
                                            <?= safe($post['slug']) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-eye me-1"></i>
                                            <?= number_format($post['views'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($post['ativo']): ?>
                                            <span class="badge bg-success">Publicado</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Rascunho</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="index.php?page=artigo&slug=<?= safe($post['slug']) ?>" target="_blank"
                                            class="btn btn-sm btn-outline-primary me-1" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="index.php?page=admin&action=blog_form&id=<?= $post['id'] ?>"
                                            class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmarExclusao(<?= $post['id'] ?>, '<?= safe($post['titulo']) ?>')"
                                            title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
                <p>Deseja realmente excluir o artigo "<strong id="nomePetExcluir"></strong>"?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
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
        document.getElementById('nomePetExcluir').textContent = titulo;
        document.getElementById('btnConfirmarExclusao').href = 'index.php?page=admin&action=blog_excluir&id=' + id;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>