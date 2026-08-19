<?php
/**
 * ============================================================
 * VIEW: LISTA DE COTAÇÕES - ADMIN
 * ============================================================
 */

$pageTitle = 'Cotações - Curicas Pet Admin';
$isAdmin = true;

// Iniciar buffer para o conteúdo
ob_start();
?>

<div class="d-flex">
    <!-- Sidebar -->
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4" style="background: #f1f5f9; min-height: 100vh;">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Cotações / Orçamentos</h4>
                <p class="text-muted mb-0">Gerencie as solicitações de orçamento</p>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : $flash['type']) ?> alert-dismissible fade show"
                role="alert">
                <?= safe($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body py-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="index.php?page=admin&action=cotacoes"
                        class="btn btn-sm <?= !isset($_GET['status']) ? 'btn-primary' : 'btn-outline-primary' ?>">
                        Todas <span class="badge bg-white text-primary ms-1">
                            <?= array_sum($contadores) ?>
                        </span>
                    </a>
                    <a href="index.php?page=admin&action=cotacoes&status=pendente"
                        class="btn btn-sm <?= ($_GET['status'] ?? '') === 'pendente' ? 'btn-warning' : 'btn-outline-warning' ?>">
                        Pendentes <span class="badge bg-white text-warning ms-1">
                            <?= $contadores['pendente'] ?>
                        </span>
                    </a>
                    <a href="index.php?page=admin&action=cotacoes&status=respondida"
                        class="btn btn-sm <?= ($_GET['status'] ?? '') === 'respondida' ? 'btn-info' : 'btn-outline-info' ?>">
                        Respondidas <span class="badge bg-white text-info ms-1">
                            <?= $contadores['respondida'] ?>
                        </span>
                    </a>
                    <a href="index.php?page=admin&action=cotacoes&status=convertida"
                        class="btn btn-sm <?= ($_GET['status'] ?? '') === 'convertida' ? 'btn-success' : 'btn-outline-success' ?>">
                        Convertidas <span class="badge bg-white text-success ms-1">
                            <?= $contadores['convertida'] ?>
                        </span>
                    </a>
                    <a href="index.php?page=admin&action=cotacoes&status=cancelada"
                        class="btn btn-sm <?= ($_GET['status'] ?? '') === 'cancelada' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                        Canceladas <span class="badge bg-white text-secondary ms-1">
                            <?= $contadores['cancelada'] ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista de Cotações -->
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-0">
                <?php if (empty($cotacoes)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4"></i>
                        <p class="mt-2">Nenhuma cotação encontrada</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Contato</th>
                                    <th>Serviços</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotacoes as $cotacao): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">
                                                <?= safe($cotacao['nome_cliente']) ?>
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                <?= $cotacao['quantidade_pets'] ?> pet(s)
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="bi bi-envelope me-1"></i>
                                                <?= safe($cotacao['email']) ?>
                                                <?php if ($cotacao['telefone']): ?>
                                                    <br><i class="bi bi-phone me-1"></i>
                                                    <?= safe($cotacao['telefone']) ?>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php foreach ($cotacao['servicos_selecionados'] as $serv): ?>
                                                <span class="badge bg-light text-dark">
                                                    <?= safe($serv['nome'] ?? 'Serviço') ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">R$
                                                <?= number_format($cotacao['valor_final'], 2, ',', '.') ?>
                                            </span>
                                            <?php if ($cotacao['desconto_aplicado'] > 0): ?>
                                                <br><small class="text-muted"><del>R$
                                                        <?= number_format($cotacao['valor_estimado'], 2, ',', '.') ?>
                                                    </del></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClasses = [
                                                'pendente' => 'bg-warning',
                                                'respondida' => 'bg-info',
                                                'convertida' => 'bg-success',
                                                'cancelada' => 'bg-secondary'
                                            ];
                                            ?>
                                            <span class="badge <?= $statusClasses[$cotacao['status']] ?>">
                                                <?= ucfirst($cotacao['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                <?= date('d/m/Y', strtotime($cotacao['created_at'])) ?>
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <?= date('H:i', strtotime($cotacao['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="index.php?page=admin&action=cotacao-detalhe&id=<?= $cotacao['id'] ?>"
                                                    class="btn btn-outline-primary" title="Ver Detalhes">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-success dropdown-toggle"
                                                    data-bs-toggle="dropdown" title="Alterar Status">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <h6 class="dropdown-header">Alterar Status</h6>
                                                    </li>
                                                    <?php foreach (['pendente', 'respondida', 'convertida', 'cancelada'] as $status): ?>
                                                        <li>
                                                            <form method="POST" action="index.php?page=admin&action=cotacao-status"
                                                                class="d-inline">
                                                                <?= csrfField() ?>
                                                                <input type="hidden" name="id" value="<?= $cotacao['id'] ?>">
                                                                <input type="hidden" name="status" value="<?= $status ?>">
                                                                <button type="submit"
                                                                    class="dropdown-item <?= $cotacao['status'] === $status ? 'active' : '' ?>">
                                                                    <?= ucfirst($status) ?>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
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
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>