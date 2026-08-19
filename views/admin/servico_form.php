<?php
/**
 * ============================================================
 * VIEW: FORMULÁRIO DE SERVIÇO - ADMIN
 * ============================================================
 */

$pageTitle = ($isEdit ? 'Editar' : 'Novo') . ' Serviço - Curicas Pet Admin';
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
                <h4 class="fw-bold mb-1">
                    <?= $isEdit ? 'Editar Serviço' : 'Novo Serviço' ?>
                </h4>
                <p class="text-muted mb-0">
                    <a href="index.php?page=admin&action=servicos" class="text-decoration-none">Serviços</a>
                    <i class="bi bi-chevron-right mx-1"></i>
                    <?= $isEdit ? safe($servico['nome']) : 'Criar' ?>
                </p>
            </div>
        </div>

        <!-- Formulário -->
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= implode('<br>', array_map('safe', $errors)) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=admin&action=servico-salvar">
                    <?= csrfField() ?>

                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $servico['id'] ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome do Serviço *</label>
                                <input type="text" class="form-control" name="nome" required
                                    value="<?= safe($servico['nome'] ?? '') ?>" placeholder="Ex: Hotel Premium">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ícone Bootstrap</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="iconPreview">
                                        <i class="bi <?= safe($servico['icone_bootstrap'] ?? 'bi-star') ?>"></i>
                                    </span>
                                    <input type="text" class="form-control" name="icone_bootstrap" id="iconeInput"
                                        value="<?= safe($servico['icone_bootstrap'] ?? 'bi-star') ?>"
                                        placeholder="bi-house-heart">
                                </div>
                                <div class="form-text">
                                    <a href="https://icons.getbootstrap.com/" target="_blank">Ver lista de ícones</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição *</label>
                        <textarea class="form-control" name="descricao" rows="3" required
                            placeholder="Descreva o serviço detalhadamente..."><?= safe($servico['descricao'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-image text-primary me-1"></i>URL da Imagem
                        </label>
                        <input type="url" class="form-control" name="imagem_url" id="imagemUrl"
                            value="<?= safe($servico['imagem_url'] ?? '') ?>"
                            placeholder="https://seusite.com/imagens/servico.jpg">
                        <div class="form-text">
                            Deixe vazio para usar imagem padrão. Tamanho recomendado: 600x400px
                        </div>
                        <?php 
                        $imagemPreview = $servico['imagem_url'] ?? '';
                        if (empty($imagemPreview)) {
                            $imagemPreview = 'https://placehold.co/600x400/6366f1/white?text=Sem+Imagem';
                        }
                        ?>
                        <div class="mt-2">
                            <img id="imagemPreview" src="<?= safe($imagemPreview) ?>" 
                                 alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Preço Base (R$) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" name="preco_base" required step="0.01"
                                        min="0" value="<?= $servico['preco_base'] ?? '' ?>" placeholder="120.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tipo de Cálculo *</label>
                                <select class="form-select" name="tipo_calculo" required>
                                    <option value="diaria" <?= ($servico['tipo_calculo'] ?? '') === 'diaria' ? 'selected' : '' ?>>
                                        Por Diária (Hotel, Hospedagem)
                                    </option>
                                    <option value="unidade" <?= ($servico['tipo_calculo'] ?? '') === 'unidade' ? 'selected' : '' ?>>
                                        Por Unidade (Passeios, Banhos)
                                    </option>
                                    <option value="hora" <?= ($servico['tipo_calculo'] ?? '') === 'hora' ? 'selected' : '' ?>>
                                        Por Hora (Pet Sitter)
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo"
                                        <?= ($servico['ativo'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ativo">
                                        Serviço ativo (visível no site)
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-eye-slash text-warning me-1"></i>Ocultar Preço
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           name="ocultar_preco" id="ocultar_preco"
                                           <?= ($servico['ocultar_preco'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ocultar_preco">
                                        Ocultar valor (exibe botão WhatsApp)
                                    </label>
                                </div>
                                <div class="form-text text-warning">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Quando ativo, o cliente vê uma mensagem de contato em vez do preço.
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Salvar Alterações' : 'Criar Serviço' ?>
                        </button>
                        <a href="index.php?page=admin&action=servicos" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
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
    // Preview do ícone em tempo real
    document.getElementById('iconeInput').addEventListener('input', function () {
        const preview = document.getElementById('iconPreview');
        preview.innerHTML = '<i class="bi ' + this.value + '"></i>';
    });
</script>
<?php
$scripts = ob_get_clean();

require __DIR__ . '/../layouts/base.php';
?>