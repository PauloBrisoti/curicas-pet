<?php
/**
 * ============================================================
 * VIEW ADMIN: LISTA DE PETS PARA ADOÇÃO
 * ============================================================
 */

$pageTitle = 'Gerenciar Pets - Admin';
$isAdmin = true;
$bodyClass = 'admin-page';

ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="bi bi-heart me-2"></i>Pets para Adoção</h1>
                <a href="index.php?page=admin&action=pet-novo" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Novo Pet
                </a>
            </div>

            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
                    <?= $_SESSION['flash_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            <?php endif; ?>

            <!-- Estatísticas -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <div>
                                <div class="stats-number">
                                    <?= $contagem['disponivel'] ?? 0 ?>
                                </div>
                                <div class="text-muted">Disponíveis</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <div class="stats-number">
                                    <?= $contagem['reservado'] ?? 0 ?>
                                </div>
                                <div class="text-muted">Reservados</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-house-heart-fill"></i>
                            </div>
                            <div>
                                <div class="stats-number">
                                    <?= $contagem['adotado'] ?? 0 ?>
                                </div>
                                <div class="text-muted">Adotados</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Pets -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if (empty($pets)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-emoji-smile display-1 text-muted"></i>
                            <p class="mt-3 text-muted">Nenhum pet cadastrado ainda.</p>
                            <a href="index.php?page=admin&action=pet-novo" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Cadastrar Primeiro Pet
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nome</th>
                                        <th>Idade</th>
                                        <th>Porte</th>
                                        <th>Sexo</th>
                                        <th>Status</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pets as $pet): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($pet['imagem'])): ?>
                                                    <img src="<?= safe($pet['imagem']) ?>" alt="<?= safe($pet['nome']) ?>"
                                                        class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong>
                                                    <?= safe($pet['nome']) ?>
                                                </strong>
                                                <br><small class="text-muted">
                                                    <?= safe($pet['raca']) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?= safe($pet['idade']) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= safe($pet['porte']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge"
                                                    style="background: <?= $pet['sexo'] === 'M' ? '#3B82F6' : '#EC4899' ?>;">
                                                    <?= $pet['sexo'] === 'M' ? '♂ Macho' : '♀ Fêmea' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusBadge = match ($pet['status']) {
                                                    'disponivel' => 'success',
                                                    'reservado' => 'warning',
                                                    'adotado' => 'info',
                                                    default => 'secondary'
                                                };
                                                $statusTexto = match ($pet['status']) {
                                                    'disponivel' => 'Disponível',
                                                    'reservado' => 'Reservado',
                                                    'adotado' => 'Adotado',
                                                    default => $pet['status']
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusBadge ?>">
                                                    <?= $statusTexto ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="index.php?page=admin&action=pet-editar&id=<?= $pet['id'] ?>"
                                                        class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmarExclusao(<?= $pet['id'] ?>, '<?= safe($pet['nome']) ?>')"
                                                        title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o pet <strong id="nomePetExcluir"></strong>?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnConfirmarExclusao" class="btn btn-danger">Excluir</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarExclusao(id, nome) {
        document.getElementById('nomePetExcluir').textContent = nome;
        document.getElementById('btnConfirmarExclusao').href = 'index.php?page=admin&action=pet-excluir&id=' + id;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>