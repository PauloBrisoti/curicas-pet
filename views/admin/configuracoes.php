<?php
/**
 * ============================================================
 * ADMIN VIEW: CONFIGURAÇÕES DO PAINEL
 * ============================================================
 * Permite:
 * - Upload seguro do QR Code de doação
 * - Toggle para ativar/desativar seção de doação
 * ============================================================
 */
$pageTitle = 'Configurações — Admin';
$isAdmin   = true;

ob_start();
?>
<div class="container-fluid px-4 py-4">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Configurações do Site</h1>
            <p class="text-muted small mb-0">Gerencie módulos e ativos do site</p>
        </div>
        <a href="index.php?page=admin&action=dashboard" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Alertas de feedback -->
    <?php if (!empty($successMsg)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= safe($successMsg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if (!empty($errorMsg)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?= safe($errorMsg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- ─── Módulo de Doação ─────────────────────────────────── -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-heart-fill text-danger me-2"></i>
                        Módulo de Doação / PIX
                    </h5>
                    <p class="text-muted small mb-4">
                        Gerencie a seção solidária exibida na página de Adoção.
                    </p>

                    <form method="POST"
                          action="index.php?page=admin&action=configuracoesSalvar"
                          enctype="multipart/form-data"
                          id="formConfiguracoes">

                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token"
                               value="<?= safe($_SESSION['csrf_token'] ?? '') ?>">

                        <!-- Toggle: Ativar seção de doação -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Status da Seção</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="doacaoAtiva" name="doacao_ativa" value="1"
                                       <?= ($appConfig['doacao_ativa'] ?? true) ? 'checked' : '' ?>
                                       role="switch">
                                <label class="form-check-label" for="doacaoAtiva">
                                    Exibir seção de doações na página Adote um Pet
                                </label>
                            </div>
                            <div class="form-text">
                                Quando desativado, o bloco de PIX/QR Code fica oculto para os visitantes.
                            </div>
                        </div>

                        <!-- E-mail de Apoio à Causa Animal -->
                        <div class="mb-4">
                            <label for="email_apoio" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1"></i>
                                E-mail de Apoio (Causa Animal)
                            </label>
                            <input type="email"
                                   class="form-control"
                                   id="email_apoio"
                                   name="email_apoio"
                                   value="<?= safe($appConfig['email_apoio'] ?? '') ?>"
                                   placeholder="Ex: apoio@curicaspet.com.br">
                            <div class="form-text">
                                Quando preenchido, exibe um link de contato por e-mail na seção
                                de doação. Deixe vazio para não exibir.
                            </div>
                        </div>

                        <!-- Upload do QR Code -->
                        <div class="mb-4">
                            <label for="qrcode_doacao" class="form-label fw-semibold">
                                <i class="bi bi-qr-code me-1"></i>
                                QR Code PIX (upload)
                            </label>

                            <?php
                            $qrCodePath   = APP_ROOT . '/assets/images/qrcode-doacao.png';
                            $qrCodeExiste = file_exists($qrCodePath);
                            ?>
                            <?php if ($qrCodeExiste): ?>
                            <div class="mb-3">
                                <p class="small text-success mb-1">
                                    <i class="bi bi-check-circle me-1"></i>
                                    QR Code atual:
                                </p>
                                <img src="/assets/images/qrcode-doacao.png?v=<?= time() ?>"
                                     alt="QR Code atual"
                                     width="120" height="120"
                                     class="rounded border shadow-sm">
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning py-2 small mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Nenhum QR Code cadastrado. Faça o upload abaixo.
                            </div>
                            <?php endif; ?>

                            <input type="file"
                                   class="form-control"
                                   id="qrcode_doacao"
                                   name="qrcode_doacao"
                                   accept=".png,.jpg,.jpeg,.webp">
                            <div class="form-text">
                                Formatos aceitos: PNG, JPG, WEBP. Tamanho máximo: 2 MB.<br>
                                O arquivo será salvo como <code>/assets/images/qrcode-doacao.png</code>.
                                Se já existir um QR Code, será substituído automaticamente.
                            </div>
                        </div>

                        <!-- Botão salvar -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-floppy me-2"></i>Salvar Configurações
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- ─── Info lateral ────────────────────────────────────── -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; background:#f8f9fa;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Como funciona
                    </h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-3">
                            <strong class="text-dark">Toggle Ativo:</strong><br>
                            A seção de doação e o QR Code aparecem na página pública de Adoção.
                        </li>
                        <li class="mb-3">
                            <strong class="text-dark">Toggle Inativo:</strong><br>
                            A seção fica completamente oculta. O PIX continua configurado para quando reativar.
                        </li>
                        <li class="mb-3">
                            <strong class="text-dark">Upload do QR Code:</strong><br>
                            Faça o upload em PNG 200×200px. O arquivo substitui o anterior automaticamente.
                            Certifique-se de que a pasta <code>assets/img/</code> tem permissão de escrita <strong>(chmod 755)</strong>.
                        </li>
                        <li class="mb-0">
                            <strong class="text-dark">Chave PIX:</strong><br>
                            Altere a chave em <code>config/app.php</code> → <code>'pix_chave'</code>.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div><!-- /.row -->
</div><!-- /.container-fluid -->

<?php
$content = ob_get_clean();
require APP_ROOT . '/views/layouts/base.php';
?>
