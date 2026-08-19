<?php
/**
 * ============================================================
 * VIEW: DETALHE DE COTAÇÃO - ADMIN
 * ============================================================
 */

$pageTitle = 'Cotação #' . $cotacao['id'] . ' - Curicas Pet Admin';
$isAdmin = true;

$statusClasses = [
    'pendente' => 'bg-warning',
    'respondida' => 'bg-info',
    'convertida' => 'bg-success',
    'cancelada' => 'bg-secondary'
];

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
                <h4 class="fw-bold mb-1">Cotação #<?= $cotacao['id'] ?></h4>
                <p class="text-muted mb-0">
                    <a href="index.php?page=admin&action=cotacoes" class="text-decoration-none">Cotações</a>
                    <i class="bi bi-chevron-right mx-1"></i>
                    Detalhe
                </p>
            </div>
            <span class="badge <?= $statusClasses[$cotacao['status']] ?> fs-6">
                <?= ucfirst($cotacao['status']) ?>
            </span>
        </div>
        
        <div class="row g-4">
            <!-- Informações do Cliente -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person text-primary me-2"></i>Cliente</h5>
                        
                        <p class="mb-2">
                            <strong><?= safe($cotacao['nome_cliente']) ?></strong>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-envelope me-2 text-muted"></i>
                            <a href="mailto:<?= safe($cotacao['email']) ?>"><?= safe($cotacao['email']) ?></a>
                        </p>
                        <?php if ($cotacao['telefone']): ?>
                            <p class="mb-2">
                                <i class="bi bi-phone me-2 text-muted"></i>
                                <a href="tel:<?= safe($cotacao['telefone']) ?>"><?= safe($cotacao['telefone']) ?></a>
                            </p>
                            <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $cotacao['telefone']) ?>" 
                               target="_blank" class="btn btn-success btn-sm mt-2">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <p class="mb-1 text-muted small">Quantidade de Pets</p>
                        <p class="fw-bold mb-0"><?= $cotacao['quantidade_pets'] ?> pet(s)</p>
                        
                        <?php if ($cotacao['mensagem']): ?>
                            <hr>
                            <p class="mb-1 text-muted small">Observações</p>
                            <p class="mb-0"><?= nl2br(safe($cotacao['mensagem'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Serviços Selecionados -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-receipt text-primary me-2"></i>Detalhes do Orçamento</h5>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Serviço</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-end">Preço Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cotacao['servicos_selecionados'] as $serv): ?>
                                        <tr>
                                            <td><?= safe($serv['nome'] ?? 'Serviço') ?></td>
                                            <td class="text-center">
                                                <?= $serv['quantidade'] ?? 1 ?> 
                                                <?php
                                                $tipoLabel = match($serv['tipo_calculo'] ?? 'diaria') {
                                                    'diaria' => 'diária(s)',
                                                    'unidade' => 'unidade(s)',
                                                    'hora' => 'hora(s)',
                                                    default => ''
                                                };
                                                echo $tipoLabel;
                                                ?>
                                                × <?= $cotacao['quantidade_pets'] ?> pet(s)
                                            </td>
                                            <td class="text-end">R$ <?= number_format($serv['preco_unitario'] ?? 0, 2, ',', '.') ?></td>
                                            <td class="text-end">R$ <?= number_format($serv['subtotal'] ?? 0, 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="3" class="text-end">Subtotal:</td>
                                        <td class="text-end">R$ <?= number_format($cotacao['valor_estimado'], 2, ',', '.') ?></td>
                                    </tr>
                                    <?php if ($cotacao['desconto_aplicado'] > 0): ?>
                                        <tr class="table-success">
                                            <td colspan="3" class="text-end">Desconto (10% - 2+ pets):</td>
                                            <td class="text-end text-success">- R$ <?= number_format($cotacao['desconto_aplicado'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="table-primary">
                                        <td colspan="3" class="text-end fw-bold fs-5">TOTAL:</td>
                                        <td class="text-end fw-bold fs-5">R$ <?= number_format($cotacao['valor_final'], 2, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Alterar Status -->
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-gear text-primary me-2"></i>Alterar Status</h5>
                        
                        <form method="POST" action="index.php?page=admin&action=cotacao-status" class="d-flex gap-2 flex-wrap">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= $cotacao['id'] ?>">
                            
                            <?php foreach (['pendente', 'respondida', 'convertida', 'cancelada'] as $status): ?>
                                <button type="submit" name="status" value="<?= $status ?>" 
                                        class="btn <?= $cotacao['status'] === $status ? $statusClasses[$status] : 'btn-outline-secondary' ?>">
                                    <?= ucfirst($status) ?>
                                </button>
                            <?php endforeach; ?>
                        </form>
                    </div>
                </div>
                
                <!-- Timestamps -->
                <div class="text-muted small mt-3">
                    <i class="bi bi-clock me-1"></i> Criado em: <?= date('d/m/Y H:i', strtotime($cotacao['created_at'])) ?>
                    <?php if ($cotacao['updated_at']): ?>
                        | Atualizado em: <?= date('d/m/Y H:i', strtotime($cotacao['updated_at'])) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>
