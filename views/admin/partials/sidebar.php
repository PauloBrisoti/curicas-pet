<?php
/**
 * ============================================================
 * PARTIAL: SIDEBAR ADMIN
 * ============================================================
 */

$currentAction = $_GET['action'] ?? 'dashboard';
?>

<!-- Mobile Menu Toggle -->
<button class="btn btn-dark d-lg-none position-fixed" style="top: 10px; left: 10px; z-index: 1050;" type="button"
    onclick="document.querySelector('.admin-sidebar').classList.toggle('show')">
    <i class="bi bi-list fs-4"></i>
</button>

<div class="admin-sidebar" style="width: 260px; min-width: 260px;">
    <!-- Close button for mobile -->
    <button class="btn btn-link text-white d-lg-none position-absolute" style="top: 10px; right: 10px;"
        onclick="document.querySelector('.admin-sidebar').classList.remove('show')">
        <i class="bi bi-x-lg"></i>
    </button>

    <div class="px-3 py-2 mb-3 mt-4 mt-lg-0">
        <a href="index.php" class="text-white text-decoration-none d-flex align-items-center">
            <i class="bi bi-heart-pulse-fill me-2" style="color: #8FDC4F; font-size: 1.5rem;"></i>
            <span class="fw-bold fs-5">Curicas Pet</span>
        </a>
    </div>

    <nav class="nav flex-column">
        <a class="nav-link <?= $currentAction === 'dashboard' ? 'active' : '' ?>"
            href="index.php?page=admin&action=dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link <?= in_array($currentAction, ['servicos', 'servico-novo', 'servico-editar']) ? 'active' : '' ?>"
            href="index.php?page=admin&action=servicos">
            <i class="bi bi-grid"></i> Serviços
        </a>
        <a class="nav-link <?= in_array($currentAction, ['cotacoes', 'cotacao-detalhe']) ? 'active' : '' ?>"
            href="index.php?page=admin&action=cotacoes">
            <i class="bi bi-receipt"></i> Cotações
        </a>
        <a class="nav-link <?= in_array($currentAction, ['pets', 'pet-novo', 'pet-editar']) ? 'active' : '' ?>"
            href="index.php?page=admin&action=pets">
            <i class="bi bi-heart"></i> Pets para Adoção
        </a>
        <a class="nav-link <?= in_array($currentAction, ['blog', 'blog_form']) ? 'active' : '' ?>"
            href="index.php?page=admin&action=blog">
            <i class="bi bi-journal-richtext"></i> Blog
        </a>
        <a class="nav-link <?= in_array($currentAction, ['gallery', 'gallery_form']) ? 'active' : '' ?>"
            href="index.php?page=admin&action=gallery">
            <i class="bi bi-images"></i> Galeria
        </a>
        <a class="nav-link <?= $currentAction === 'configuracoes' ? 'active' : '' ?>"
            href="index.php?page=admin&action=configuracoes">
            <i class="bi bi-gear"></i> Configurações
        </a>
    </nav>

    <div class="mt-auto p-3 border-top border-secondary" style="position: absolute; bottom: 0; width: 100%;">
        <div class="d-flex align-items-center mb-2">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                style="width: 40px; height: 40px;">
                <?= strtoupper(substr($_SESSION['admin_nome'] ?? 'A', 0, 1)) ?>
            </div>
            <div class="ms-2">
                <p class="text-white mb-0 small fw-semibold">
                    <?= safe($_SESSION['admin_nome'] ?? 'Admin') ?>
                </p>
                <p class="text-muted mb-0 small">
                    <?= safe($_SESSION['admin_email'] ?? '') ?>
                </p>
            </div>
        </div>
        <a href="index.php?page=admin&action=logout" class="btn btn-outline-light btn-sm w-100">
            <i class="bi bi-box-arrow-right me-1"></i> Sair
        </a>
    </div>
</div>