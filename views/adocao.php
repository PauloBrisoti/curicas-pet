<?php
/**
 * ============================================================
 * VIEW: ADOTE UM PET — v3.0
 * ============================================================
 * Alterações:
 * - rawurlencode() em todos os links WhatsApp
 * - Seção de doação: bg-light (sem gradiente rosa/vermelho)
 * - PIX Desktop: QR Code placeholder
 * - PIX Mobile: botão "Copiar Chave" com toast JS
 * - Aviso antifraude
 * - Credenciais PIX via config/app.php (sem hardcode)
 * ============================================================
 */

$pageTitle       = 'Serviço Solidário: Doação de Ração e Apoio Pet em Osasco | Curicas Pet';
$pageDescription = 'Apoie a causa animal em Osasco e Zona Oeste. Recebemos doações de ração, remédios e cobertores para pets resgatados. Faça a diferença com a Curicas Pet.';

// Configurações
$businessConfig = require __DIR__ . '/../config/business.php';
$appConfig      = require __DIR__ . '/../config/app.php';
$pixChave       = $appConfig['pix_chave'];
$pixTitular     = $appConfig['pix_titular'];
$whatsappBase   = 'https://wa.me/' . $businessConfig['whatsapp_num'];

// ── Configurações da seção de doação (controlado pelo admin) ─────
$doacaoAtiva    = $appConfig['doacao_ativa']    ?? true;  // padrão: ativo
$qrCodeCaminho  = APP_ROOT . '/assets/images/qrcode-doacao.png';
$qrCodeUrl      = '/assets/images/qrcode-doacao.png';
$qrCodeExiste   = file_exists($qrCodeCaminho);

ob_start();
?>

<!-- ── Hero ── -->
<section class="py-5" style="background:linear-gradient(135deg,#1a2a0a,#2D3E1F);">
    <div class="container">
        <div class="col-lg-8 mx-auto text-center animate-fade-in">
            <div class="mb-3">
                <span class="badge px-3 py-2"
                      style="background:rgba(239,68,68,.2); color:#fca5a5; border:1px solid rgba(239,68,68,.3);">
                    ❤️ +50 pets já encontraram um lar
                </span>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">
                Transforme uma Vida Hoje
            </h1>
            <p class="lead mb-4" style="color:rgba(255,255,255,.75);">
                Cada pet aqui tem uma história e está esperando por <strong style="color:#8FDC4F;">você</strong>.
                Todos estão vacinados, vermifugados e prontos para retribuir com amor incondicional.
            </p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <?php
                $badges = [
                    ['icon' => 'bi-heart',        'label' => 'Adoção Responsável'],
                    ['icon' => 'bi-shield-check',  'label' => '100% Vacinados'],
                    ['icon' => 'bi-house-heart',   'label' => 'Suporte Pós-Adoção'],
                    ['icon' => 'bi-gift',          'label' => 'Kit Boas-Vindas'],
                ];
                foreach ($badges as $b):
                ?>
                <span class="badge fw-normal px-3 py-2"
                      style="background:rgba(255,255,255,.12); color:rgba(255,255,255,.9); border:1px solid rgba(255,255,255,.2);">
                    <i class="<?= $b['icon'] ?> me-1"></i><?= $b['label'] ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ── Listagem de Pets ── -->
<section class="py-5">
    <div class="container py-2">
        <div class="text-center mb-4">
            <h2 class="display-6 fw-bold">Pets Disponíveis</h2>
            <p class="text-muted">Encontre seu novo companheiro de vida</p>
        </div>

        <!-- Filtros -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-search me-1"></i>Buscar
                        </label>
                        <input type="text" class="form-control" id="filtroNome" placeholder="Nome do pet...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-rulers me-1"></i>Porte
                        </label>
                        <select class="form-select" id="filtroPorte">
                            <option value="">Todos</option>
                            <option value="Pequeno">Pequeno</option>
                            <option value="Médio">Médio</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-gender-ambiguous me-1"></i>Sexo
                        </label>
                        <select class="form-select" id="filtroSexo">
                            <option value="">Todos</option>
                            <option value="M">♂ Macho</option>
                            <option value="F">♀ Fêmea</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="limparFiltros()">
                            <i class="bi bi-x-circle me-1"></i>Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Pets -->
        <div class="row g-4" id="petContainer">
            <?php if (empty($pets)): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-smile display-1 text-muted"></i>
                    <h4 class="mt-3">Todos os pets foram adotados!</h4>
                    <p class="text-muted">No momento não temos pets disponíveis. Volte em breve!</p>
                </div>
            <?php else: ?>
                <?php foreach ($pets as $idx => $pet):
                    $waMsg  = rawurlencode('Olá! Tenho interesse em adotar o(a) ' . $pet['nome'] . '. Gostaria de mais informações sobre ele(a)!');
                    $waLink = $whatsappBase . '?text=' . $waMsg;
                    $imgUrl = !empty($pet['imagem'])
                        ? $pet['imagem']
                        : 'https://via.placeholder.com/400x400/6BBF2D/ffffff?text=' . rawurlencode($pet['nome']);
                ?>
                <div class="col-md-6 col-lg-4 animate-fade-in delay-<?= ($idx % 3) + 1 ?> pet-card-item"
                     data-nome="<?= safe(strtolower($pet['nome'])) ?>"
                     data-porte="<?= safe($pet['porte']) ?>"
                     data-sexo="<?= safe($pet['sexo']) ?>">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius:20px;">

                        <!-- Foto -->
                        <div class="position-relative" style="height:260px; overflow:hidden;">
                            <img src="<?= safe($imgUrl) ?>"
                                 alt="<?= safe($pet['nome']) ?> — para adoção em Osasco"
                                 class="w-100 h-100"
                                 style="object-fit:contain; object-position:center; background:linear-gradient(135deg,#f8faf5,#e8f5e0);"
                                 loading="lazy">

                            <!-- Badges sobrepostos -->
                            <span class="badge position-absolute top-0 start-0 m-3"
                                  style="background:<?= $pet['sexo'] === 'M' ? '#3B82F6' : '#EC4899' ?>;">
                                <?= $pet['sexo'] === 'M' ? '♂ Macho' : '♀ Fêmea' ?>
                            </span>
                            <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-3">
                                <?= safe($pet['porte']) ?>
                            </span>
                        </div>

                        <!-- Corpo -->
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h3 class="card-title h5 fw-bold mb-0"><?= safe($pet['nome']) ?></h3>
                                <span class="text-muted small"><?= safe($pet['idade']) ?></span>
                            </div>

                            <p class="text-muted small mb-2">
                                <i class="bi bi-tag me-1"></i><?= safe($pet['raca']) ?>
                            </p>

                            <p class="card-text text-muted small flex-grow-1">
                                <?= safe($pet['descricao']) ?>
                            </p>

                            <!-- Status Badges -->
                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                <?php if ($pet['vacinado']): ?>
                                <span class="badge" style="background:rgba(107,191,45,.15); color:#4A9B2E;">
                                    <i class="bi bi-check-circle me-1"></i>Vacinado
                                </span>
                                <?php endif; ?>
                                <?php if ($pet['castrado']): ?>
                                <span class="badge" style="background:rgba(59,130,246,.12); color:#1d4ed8;">
                                    <i class="bi bi-check-circle me-1"></i>Castrado
                                </span>
                                <?php endif; ?>
                            </div>

                            <!-- CTA WhatsApp -->
                            <a href="<?= safe($waLink) ?>" target="_blank" rel="noopener"
                               class="btn btn-whatsapp w-100 fw-semibold">
                                <i class="fab fa-whatsapp me-2"></i>Quero Adotar!
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ── Como Funciona ── -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Como Funciona a Adoção?</h2>
            <p class="text-muted">Processo simples e transparente</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $passos = [
                ['emoji' => '📱', 'num' => '1', 'titulo' => 'Entre em Contato',  'desc' => 'Clique em "Quero Adotar" do pet que te interessou e fale conosco pelo WhatsApp.'],
                ['emoji' => '📋', 'num' => '2', 'titulo' => 'Entrevista',        'desc' => 'Uma conversa rápida para garantir o melhor lar para o pet.'],
                ['emoji' => '🏠', 'num' => '3', 'titulo' => 'Lar Doce Lar',      'desc' => 'Após aprovação, o pet vai para sua casa com acompanhamento pós-adoção.'],
            ];
            foreach ($passos as $p):
            ?>
            <div class="col-md-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:64px;height:64px;background:linear-gradient(135deg,#8FDC4F,#4A9B2E);font-size:1.3rem;color:#fff;font-weight:700;font-size:1.4rem;">
                    <?= $p['num'] ?>
                </div>
                <h5 class="fw-bold"><?= $p['titulo'] ?></h5>
                <p class="text-muted small"><?= $p['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     SEÇÃO SOLIDÁRIA — Ponto de Doação em Osasco
     Condicional: ativa via painel admin
     ═══════════════════════════════════════════ -->
<?php if ($doacaoAtiva): ?>
<section class="py-5" style="background:#F8F9FA;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 p-lg-5" style="border-radius:24px;">
                    <div class="text-center mb-4">
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 mb-2">
                            <i class="bi bi-heart-fill me-1"></i> Serviço Solidário
                        </span>
                        <h2 class="h3 fw-bold">Ponto de Doação de Ração em Osasco</h2>
                        <p class="text-muted">
                            Sua doação mantém nossos pets saudáveis e felizes até encontrarem um lar!
                        </p>
                    </div>

                    <?php if ($qrCodeExiste): ?>
                    <!-- ── DESKTOP: QR Code + Chave ── -->
                    <div class="d-none d-md-flex align-items-center gap-4 justify-content-center mb-4">
                        <div class="text-center">
                            <img src="<?= safe($qrCodeUrl) ?>"
                                 alt="QR Code PIX Curicas Pet — Doação em Osasco"
                                 width="200" height="200"
                                 class="rounded-3 shadow-sm border">
                            <p class="small text-muted mt-2 mb-0">
                                Escaneie com o app do banco
                            </p>
                        </div>
                        <div>
                            <p class="fw-semibold mb-1">Chave PIX:</p>
                            <code class="fs-5 d-block mb-1"><?= safe($pixChave) ?></code>
                            <p class="text-muted small mb-0">
                                Favorecido: <strong><?= safe($pixTitular) ?></strong>
                            </p>
                        </div>
                    </div>

                    <!-- ── MOBILE: Botão Copiar ── -->
                    <div class="d-md-none text-center mb-4">
                        <p class="fw-semibold mb-1">Chave PIX:</p>
                        <code class="d-block fs-6 mb-3 p-2 rounded"
                              style="background:#f0f4ec; word-break:break-all;">
                            <?= safe($pixChave) ?>
                        </code>
                        <button class="btn btn-success btn-lg w-100 fw-bold"
                                id="btnCopiarPix"
                                onclick="copiarPix('<?= safe($pixChave) ?>')"
                                style="border-radius:50px;">
                            <i class="bi bi-clipboard me-2"></i>Copiar Chave PIX
                        </button>
                    </div>
                    <?php else: ?>
                    <!-- Sem QR Code: exibir apenas chave texto -->
                    <div class="text-center mb-4">
                        <p class="fw-semibold mb-1">Chave PIX:</p>
                        <code class="d-block fs-5 mb-3 p-3 rounded"
                              style="background:#f0f4ec; word-break:break-all;">
                            <?= safe($pixChave) ?>
                        </code>
                        <button class="btn btn-success btn-lg px-5 fw-bold"
                                id="btnCopiarPix"
                                onclick="copiarPix('<?= safe($pixChave) ?>')"
                                style="border-radius:50px;">
                            <i class="bi bi-clipboard me-2"></i>Copiar Chave PIX
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- ── ANTIFRAUDE (visível em ambos) ── -->
                    <div class="alert d-flex align-items-start gap-3 mb-3"
                         style="background:#fffbea; border:1px solid #f59e0b; border-radius:14px;">
                        <i class="bi bi-shield-exclamation fs-4 flex-shrink-0" style="color:#d97706;"></i>
                        <div class="small">
                            <strong>⚠️ Atenção Antifraude:</strong>
                            Antes de concluir a transferência, confirme se o recebedor aparece como
                            <strong>&ldquo;<?= safe($pixTitular) ?>&rdquo;</strong> no seu aplicativo bancário.
                            Nunca transfira se os dados forem diferentes.
                        </div>
                    </div>
                    <!-- Missão solidária — copy oficial -->
                    <div class="border-top pt-3 mt-2">
                        <p class="text-muted small mb-0">
                            <i class="bi bi-heart-fill me-1 text-danger"></i>
                            <strong>Apoio à Causa Animal:</strong>
                            Todos os recursos recebidos são aplicados diretamente na alimentação,
                            cuidados veterinários e manutenção dos cães de resgate que estão sob nossos cuidados.
                            Sua ajuda fortalece nossa rede de suporte a cuidadores independentes e ONGs parceiras,
                            garantindo o bem-estar dos pets até que encontrem um lar definitivo.
                        </p>
                    </div>

                </div><!-- /.card-body -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section>
<?php endif; /* $doacaoAtiva */ ?>

<!-- Schema.org DonateAction — SEO da página solidária -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "DonateAction",
    "name": "Doação de Ração e Apoio a Pets Resgatados em Osasco",
    "description": "Ponto de arrecadação de ração, remédios e cobertores para cães e gatos resgatados em Osasco e Zona Oeste SP. Apoie a causa animal com a Curicas Pet.",
    "url": "https://curicaspet.com.br/adocao",
    "recipient": {
        "@type": "Organization",
        "name": "Curicas Pet",
        "url": "https://curicaspet.com.br",
        "telephone": "+55<?= preg_replace('/\D/', '', $businessConfig['whatsapp_num'] ?? '') ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Praça Padroeira do Brasil, 111",
            "addressLocality": "Osasco",
            "addressRegion": "SP",
            "addressCountry": "BR"
        }
    },
    "object": {
        "@type": "Thing",
        "name": "Ração, Remédios e Cobertores para Pets",
        "description": "Arrecadação de ração em Osasco para cães e gatos resgatados na Zona Oeste de SP"
    },
    "location": {
        "@type": "Place",
        "name": "Curicas Pet — Centro Osasco",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Praça Padroeira do Brasil, 111",
            "addressLocality": "Osasco",
            "addressRegion": "SP",
            "addressCountry": "BR"
        }
    }
}
</script>

<!-- ── CTA: Não encontrou ── -->
<section class="py-5 bg-white">
    <div class="container text-center py-3">
        <h2 class="h3 fw-bold mb-2">Não Encontrou seu Match?</h2>
        <p class="text-muted mb-4">Fale conosco! Podemos ajudar você a encontrar o pet perfeito.</p>
        <a href="<?= $whatsappBase . '?text=' . rawurlencode('Olá! Estou procurando um pet para adotar. Vocês podem me ajudar?') ?>"
           target="_blank" rel="noopener"
           class="btn btn-primary btn-lg px-5">
            <i class="bi bi-chat-dots me-2"></i>Falar com a Equipe
        </a>
    </div>
</section>

<style>
/* Cards de pets */
.pet-card-item.hidden { display: none !important; }
</style>

<?php
$content = ob_get_clean();

ob_start();
?>
<script>
// ── Copiar Chave PIX com feedback toast ──────────────────────────
function copiarPix(chave) {
    const btn = document.getElementById('btnCopiarPix');

    if (navigator.clipboard && window.isSecureContext) {
        // API moderna (HTTPS)
        navigator.clipboard.writeText(chave).then(function () {
            mostrarSucessoCopy(btn);
        }).catch(function () {
            fallbackCopy(chave, btn);
        });
    } else {
        // Fallback para HTTP/iOS antigo
        fallbackCopy(chave, btn);
    }
}

function fallbackCopy(texto, btn) {
    const ta = document.createElement('textarea');
    ta.value = texto;
    ta.style.position = 'fixed';
    ta.style.opacity  = '0';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try { document.execCommand('copy'); mostrarSucessoCopy(btn); }
    catch (e) { alert('Copie manualmente: ' + texto); }
    document.body.removeChild(ta);
}

function mostrarSucessoCopy(btn) {
    const original = btn.innerHTML;
    btn.innerHTML   = '<i class="bi bi-check-circle me-2"></i>Copiado com sucesso!';
    btn.classList.replace('btn-success', 'btn-outline-success');
    btn.disabled = true;

    setTimeout(function () {
        btn.innerHTML = original;
        btn.classList.replace('btn-outline-success', 'btn-success');
        btn.disabled = false;
    }, 3000);
}

// ── Filtros de pets ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const filtroNome  = document.getElementById('filtroNome');
    const filtroPorte = document.getElementById('filtroPorte');
    const filtroSexo  = document.getElementById('filtroSexo');
    const petCards    = document.querySelectorAll('.pet-card-item');

    function filtrarPets() {
        const nome  = filtroNome.value.toLowerCase().trim();
        const porte = filtroPorte.value;
        const sexo  = filtroSexo.value;
        let visible = 0;

        petCards.forEach(function (card) {
            const ok =
                (!nome  || (card.dataset.nome  || '').includes(nome))  &&
                (!porte || card.dataset.porte === porte)                 &&
                (!sexo  || card.dataset.sexo  === sexo);

            card.classList.toggle('hidden', !ok);
            if (ok) visible++;
        });

        // Mensagem "sem resultados"
        const container  = document.getElementById('petContainer');
        let noResults    = container.querySelector('.no-results');
        if (visible === 0 && petCards.length > 0) {
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.className = 'col-12 text-center py-5 no-results';
                noResults.innerHTML = '<i class="bi bi-search display-1 text-muted"></i><h4 class="mt-3">Nenhum pet encontrado</h4><p class="text-muted">Tente ajustar os filtros.</p>';
                container.appendChild(noResults);
            }
        } else if (noResults) {
            noResults.remove();
        }
    }

    window.limparFiltros = function () {
        filtroNome.value  = '';
        filtroPorte.value = '';
        filtroSexo.value  = '';
        filtrarPets();
    };

    filtroNome.addEventListener('input',   filtrarPets);
    filtroPorte.addEventListener('change', filtrarPets);
    filtroSexo.addEventListener('change',  filtrarPets);
});
</script>
<?php
$scripts = ob_get_clean();

require __DIR__ . '/layouts/base.php';
?>