<?php
/**
 * ============================================================
 * VIEW ADMIN: FORMULÁRIO DE PET (NOVO/EDITAR)
 * ============================================================
 */

$isEditing = isset($pet) && $pet;
$pageTitle = $isEditing ? 'Editar Pet - Admin' : 'Novo Pet - Admin';
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
                <h1 class="h2">
                    <i class="bi bi-<?= $isEditing ? 'pencil' : 'plus-circle' ?> me-2"></i>
                    <?= $isEditing ? 'Editar Pet' : 'Novo Pet' ?>
                </h1>
                <a href="index.php?page=admin&action=pets" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>

            <?php if (isset($erro)): ?>
                <div class="alert alert-danger">
                    <?= safe($erro) ?>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="index.php?page=admin&action=pet-salvar" method="POST" enctype="multipart/form-data">
                        <?php if ($isEditing): ?>
                            <input type="hidden" name="id" value="<?= $pet['id'] ?>">
                        <?php endif; ?>

                        <div class="row g-4">
                            <!-- Coluna da Esquerda - Dados -->
                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <!-- Nome -->
                                    <div class="col-md-6">
                                        <label class="form-label">Nome do Pet *</label>
                                        <input type="text" name="nome" class="form-control" required
                                            value="<?= safe($pet['nome'] ?? '') ?>"
                                            placeholder="Ex: Thor, Luna, Bob...">
                                    </div>

                                    <!-- Idade -->
                                    <div class="col-md-6">
                                        <label class="form-label">Idade *</label>
                                        <input type="text" name="idade" class="form-control" required
                                            value="<?= safe($pet['idade'] ?? '') ?>"
                                            placeholder="Ex: 2 anos, 6 meses...">
                                    </div>

                                    <!-- Raça -->
                                    <div class="col-md-6">
                                        <label class="form-label">Raça</label>
                                        <input type="text" name="raca" class="form-control"
                                            value="<?= safe($pet['raca'] ?? 'SRD') ?>"
                                            placeholder="Ex: Labrador Mix, Vira-lata...">
                                    </div>

                                    <!-- Porte -->
                                    <div class="col-md-3">
                                        <label class="form-label">Porte *</label>
                                        <select name="porte" class="form-select" required>
                                            <option value="Pequeno" <?= ($pet['porte'] ?? '') === 'Pequeno' ? 'selected' : '' ?>>Pequeno</option>
                                            <option value="Médio" <?= ($pet['porte'] ?? 'Médio') === 'Médio' ? 'selected' : '' ?>>Médio</option>
                                            <option value="Grande" <?= ($pet['porte'] ?? '') === 'Grande' ? 'selected' : '' ?>>Grande</option>
                                        </select>
                                    </div>

                                    <!-- Sexo -->
                                    <div class="col-md-3">
                                        <label class="form-label">Sexo *</label>
                                        <select name="sexo" class="form-select" required>
                                            <option value="M" <?= ($pet['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>♂
                                                Macho</option>
                                            <option value="F" <?= ($pet['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>♀
                                                Fêmea</option>
                                        </select>
                                    </div>

                                    <!-- Descrição -->
                                    <div class="col-12">
                                        <label class="form-label">Descrição *</label>
                                        <textarea name="descricao" class="form-control" rows="4" required
                                            placeholder="Descreva a personalidade, comportamento e características do pet..."><?= safe($pet['descricao'] ?? '') ?></textarea>
                                    </div>

                                    <!-- Checkboxes -->
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="vacinado" value="1" class="form-check-input"
                                                id="vacinado" <?= ($pet['vacinado'] ?? 1) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="vacinado">
                                                <i class="bi bi-shield-check text-success me-1"></i>Vacinado
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="castrado" value="1" class="form-check-input"
                                                id="castrado" <?= ($pet['castrado'] ?? 0) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="castrado">
                                                <i class="bi bi-check-circle text-info me-1"></i>Castrado
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Status (somente edição) -->
                                    <?php if ($isEditing): ?>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="disponivel" <?= ($pet['status'] ?? '') === 'disponivel' ? 'selected' : '' ?>>
                                                    🟢 Disponível
                                                </option>
                                                <option value="reservado" <?= ($pet['status'] ?? '') === 'reservado' ? 'selected' : '' ?>>
                                                    🟡 Reservado
                                                </option>
                                                <option value="adotado" <?= ($pet['status'] ?? '') === 'adotado' ? 'selected' : '' ?>>
                                                    🔵 Adotado
                                                </option>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Coluna da Direita - Foto -->
                            <div class="col-lg-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <label class="form-label d-block mb-3">Foto do Pet</label>

                                        <div id="previewContainer" class="mb-3">
                                            <?php if ($isEditing && !empty($pet['imagem'])): ?>
                                                <img src="<?= safe($pet['imagem']) ?>" id="previewImage"
                                                    class="img-fluid rounded" style="max-height: 200px;">
                                            <?php else: ?>
                                                <div id="placeholderImage" class="bg-white rounded p-5">
                                                    <i class="bi bi-camera display-1 text-muted"></i>
                                                    <p class="text-muted mt-2 mb-0">Nenhuma foto selecionada</p>
                                                </div>
                                                <img src="" id="previewImage" class="img-fluid rounded d-none"
                                                    style="max-height: 200px;">
                                            <?php endif; ?>
                                        </div>

                                        <input type="file" name="foto" id="inputFoto" class="form-control"
                                            accept="image/*">
                                        <small class="text-muted d-block mt-2">Formatos: JPG, PNG, WEBP. Máx:
                                            5MB</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                <?= $isEditing ? 'Salvar Alterações' : 'Cadastrar Pet' ?>
                            </button>
                            <a href="index.php?page=admin&action=pets" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Preview da imagem
    document.getElementById('inputFoto').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('previewImage');
                const placeholder = document.getElementById('placeholderImage');

                preview.src = e.target.result;
                preview.classList.remove('d-none');

                if (placeholder) {
                    placeholder.classList.add('d-none');
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>