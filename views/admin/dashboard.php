<?php
/**
 * ============================================================
 * VIEW: DASHBOARD ADMIN
 * ============================================================
 */

$pageTitle = 'Dashboard - Curicas Pet Admin';
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
                <h4 class="fw-bold mb-1">Dashboard</h4>
                <p class="text-muted mb-0">Bem-vindo(a),
                    <?= safe($_SESSION['admin_nome'] ?? 'Admin') ?>!
                </p>
            </div>
            <div>
                <span class="text-muted">
                    <?= date('d/m/Y H:i') ?>
                </span>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : $flash['type']) ?> alert-dismissible fade show"
                role="alert">
                <?= safe($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-grid"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0">Serviços Ativos</p>
                            <h3 class="stats-number mb-0">
                                <?= $stats['total_servicos'] ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0">Cotações Pendentes</p>
                            <h3 class="stats-number mb-0">
                                <?= $stats['cotacoes_por_status']['pendente'] ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0">Convertidas</p>
                            <h3 class="stats-number mb-0">
                                <?= $stats['cotacoes_por_status']['convertida'] ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0">Valor Convertido</p>
                            <h3 class="stats-number mb-0">R$
                                <?= number_format($stats['valor_convertido'], 0, ',', '.') ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cotações Recentes -->
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-primary me-2"></i>Cotações Recentes</h5>
                    <a href="index.php?page=admin&action=cotacoes" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($cotacoesRecentes)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox display-4"></i>
                        <p class="mt-2">Nenhuma cotação recente</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Email</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotacoesRecentes as $cotacao): ?>
                                    <tr>
                                        <td class="fw-semibold">
                                            <?= safe($cotacao['nome_cliente']) ?>
                                        </td>
                                        <td>
                                            <?= safe($cotacao['email']) ?>
                                        </td>
                                        <td>R$
                                            <?= number_format($cotacao['valor_final'], 2, ',', '.') ?>
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
                                            <?= date('d/m H:i', strtotime($cotacao['created_at'])) ?>
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