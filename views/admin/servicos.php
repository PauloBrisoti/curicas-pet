<?php
/**
 * ============================================================
 * VIEW: LISTA DE SERVIÇOS - ADMIN
 * ============================================================
 * Versão v3.0 — toggles via POST+CSRF (sem GET inseguro)
 * ============================================================
 */

$pageTitle = 'Gerenciar Serviços - Curicas Pet Admin';
$isAdmin   = true;

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
                <h4 class="fw-bold mb-1">Gerenciar Serviços</h4>
                <p class="text-muted mb-0">Adicione, edite ou desative serviços</p>
            </div>
            <a href="index.php?page=admin&action=servico-novo" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Novo Serviço
            </a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : $flash['type']) ?> alert-dismissible fade show"
                role="alert">
                <?= safe($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Lista de Serviços -->
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-0">
                <?php if (empty($servicos)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-grid display-4"></i>
                        <p class="mt-2">Nenhum serviço cadastrado</p>
                        <a href="index.php?page=admin&action=servico-novo" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Criar Primeiro Serviço
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th>Serviço</th>
                                    <th>Preço / Visibilidade</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($servicos as $servico): ?>
                                    <tr class="<?= !$servico['ativo'] ? 'table-secondary opacity-75' : '' ?>">
                                        <td>
                                            <div style="width:45px;height:45px;background:linear-gradient(135deg,#8FDC4F,#6BBF2D);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;">
                                                <i class="bi <?= safe($servico['icone_bootstrap']) ?>"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold"><?= safe($servico['nome']) ?></span><br>
                                            <small class="text-muted"><?= safe(substr($servico['descricao'], 0, 60)) ?>...</small>
                                        </td>
                                        <td>
                                            <?php if ($servico['ocultar_preco']): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-eye-slash me-1"></i>Preço Oculto
                                                </span>
                                            <?php else: ?>
                                                <span class="fw-bold text-success">
                                                    R$ <?= number_format($servico['preco_base'], 2, ',', '.') ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <?php
                                                switch ($servico['tipo_calculo']) {
                                                    case 'diaria':  echo 'Por Diária';  break;
                                                    case 'unidade': echo 'Por Unidade'; break;
                                                    case 'hora':    echo 'Por Hora';    break;
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($servico['ativo']): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Editar -->
                                                <a href="index.php?page=admin&action=servico-editar&id=<?= $servico['id'] ?>"
                                                   class="btn btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <!-- Toggle Ativo (POST+CSRF) -->
                                                <button type="button"
                                                        class="btn btn-outline-warning"
                                                        title="<?= $servico['ativo'] ? 'Desativar' : 'Ativar' ?>"
                                                        onclick="confirmarToggleAtivo(<?= $servico['id'] ?>, <?= (int)$servico['ativo'] ?>)">
                                                    <i class="bi bi-<?= $servico['ativo'] ? 'eye-slash' : 'eye' ?>"></i>
                                                </button>
                                                <!-- Toggle Ocultar Preço (POST+CSRF) -->
                                                <button type="button"
                                                        class="btn btn-<?= $servico['ocultar_preco'] ? 'warning' : 'outline-secondary' ?>"
                                                        title="<?= $servico['ocultar_preco'] ? 'Mostrar preço' : 'Ocultar preço' ?>"
                                                        onclick="confirmarToggleOcultar(<?= $servico['id'] ?>, <?= (int)$servico['ocultar_preco'] ?>)">
                                                    <i class="bi bi-<?= $servico['ocultar_preco'] ? 'tag' : 'tag-x' ?>"></i>
                                                </button>
                                                <!-- Excluir -->
                                                <button type="button" class="btn btn-outline-danger"
                                                        onclick="confirmarExclusao(<?= $servico['id'] ?>, '<?= safe($servico['nome']) ?>')"
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
    </div>
</div>

<!-- ── FORMS OCULTOS (POST + CSRF) ── -->

<!-- Form: Toggle Ativo -->
<form id="formToggleAtivo" method="POST" action="index.php?page=admin&action=servico-toggle" style="display:none;">
    <?= csrfField() ?>
    <input type="hidden" name="id" id="toggleAtivoId">
</form>

<!-- Form: Toggle Ocultar Preço -->
<form id="formToggleOcultar" method="POST" action="index.php?page=admin&action=servico-toggle-ocultar" style="display:none;">
    <?= csrfField() ?>
    <input type="hidden" name="id" id="toggleOcultarId">
</form>

<!-- Modal: Confirmar Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o serviço <strong id="nomeServico"></strong>?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita. Considere desativar o serviço em vez de excluí-lo.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" method="POST" action="index.php?page=admin&action=servico-excluir">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" id="idServicoDeletar">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

ob_start();
?>
<script>
    // Toggle Ativo — confirma e submete via POST oculto
    function confirmarToggleAtivo(id, ativo) {
        const msg = ativo
            ? 'Desativar este serviço? Ele ficará oculto no site.'
            : 'Ativar este serviço? Ele voltará a aparecer no site.';
        if (confirm(msg)) {
            document.getElementById('toggleAtivoId').value = id;
            document.getElementById('formToggleAtivo').submit();
        }
    }

    // Toggle Ocultar Preço — confirma e submete via POST oculto
    function confirmarToggleOcultar(id, oculto) {
        const msg = oculto
            ? 'Mostrar o preço novamente? O valor será exibido no site.'
            : 'Ocultar o preço? O site exibirá um botão de WhatsApp no lugar do valor.';
        if (confirm(msg)) {
            document.getElementById('toggleOcultarId').value = id;
            document.getElementById('formToggleOcultar').submit();
        }
    }

    // Confirmar Exclusão — abre modal
    function confirmarExclusao(id, nome) {
        document.getElementById('nomeServico').textContent = nome;
        document.getElementById('idServicoDeletar').value  = id;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }
</script>
<?php
$scripts = ob_get_clean();

require __DIR__ . '/../layouts/base.php';
?>