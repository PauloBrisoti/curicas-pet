<?php
/**
 * ============================================================
 * VIEW: PÁGINA 404 - NÃO ENCONTRADO
 * ============================================================
 */

$pageTitle = 'Página não encontrada - Curicas Pet';
$pageDescription = 'Oops! A página que você procura não foi encontrada.';

ob_start();
?>

<section class="py-5" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <!-- Animação de cachorro triste -->
                <div class="mb-4" style="font-size: 120px; line-height: 1;">
                    🐕‍🦺
                </div>

                <h1 class="display-1 fw-bold" style="color: var(--primary-color);">404</h1>
                <h2 class="h3 mb-3">Oops! Página não encontrada</h2>
                <p class="text-muted mb-4">
                    Parece que este pet fugiu! A página que você procura não existe ou foi movida.
                </p>

                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-house me-2"></i>Voltar para Home
                    </a>
                    <a href="index.php?page=adocao" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-heart me-2"></i>Ver Pets para Adoção
                    </a>
                </div>

                <div class="mt-5 p-4 bg-light rounded-4">
                    <h5 class="fw-bold mb-3">🔍 O que você estava procurando?</h5>
                    <div class="row g-2 justify-content-center">
                        <div class="col-auto">
                            <a href="index.php#servicos" class="btn btn-sm btn-outline-secondary">Serviços</a>
                        </div>
                        <div class="col-auto">
                            <a href="index.php?page=orcamento" class="btn btn-sm btn-outline-secondary">Orçamento</a>
                        </div>
                        <div class="col-auto">
                            <a href="index.php?page=adocao" class="btn btn-sm btn-outline-secondary">Adoção</a>
                        </div>
                        <div class="col-auto">
                            <a href="#contato" class="btn btn-sm btn-outline-secondary">Contato</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-20px);
        }

        60% {
            transform: translateY(-10px);
        }
    }

    section>.container>.row>div>div:first-child {
        animation: bounce 2s infinite;
    }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/base.php';
?>