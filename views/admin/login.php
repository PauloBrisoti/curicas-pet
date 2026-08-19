<?php
/**
 * ============================================================
 * VIEW: LOGIN ADMIN
 * ============================================================
 */

$pageTitle = 'Login Admin - Curicas Pet';
$isAdmin = true;
$bodyClass = 'bg-gradient';

// Iniciar buffer para o conteúdo
ob_start();
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center"
    style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4">
                    <h1 class="text-white">
                        <i class="bi bi-heart-pulse-fill" style="color: #8FDC4F;"></i> Curicas Pet
                    </h1>
                    <p class="text-light opacity-75">Painel Administrativo</p>
                </div>

                <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-center mb-4">Bem-vindo!</h4>

                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : $flash['type']) ?> alert-dismissible fade show"
                                role="alert">
                                <?= safe($flash['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?= safe($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/?page=admin&action=login">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" required
                                        placeholder="seu@email.com" value="<?= safe($_POST['email'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" name="senha" required
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-light opacity-75">
                        <i class="bi bi-arrow-left me-1"></i>Voltar ao site
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>