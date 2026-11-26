<?php
/**
 * ============================================================
 * VIEW: ORÇAMENTO — v3.0 (Formulário de Lead)
 * ============================================================
 * A calculadora multi-passo foi substituída por um formulário
 * de lead limpo. O submit NÃO salva no banco — o JS monta
 * uma mensagem via rawurlencode e redireciona para wa.me.
 * ============================================================
 */

$businessConfig = require __DIR__ . '/../config/business.php';

$pageTitle       = 'Solicite seu Orçamento — Curicas Pet | Osasco e Zona Oeste SP';
$pageDescription = 'Solicite um orçamento de serviços para pets em Osasco e Zona Oeste SP pelo WhatsApp.';
$pageUrl         = $businessConfig['site_url'] . '/orcamento';

$whatsappNum  = $businessConfig['whatsapp_num'];
$whatsappBase = 'https://wa.me/' . $whatsappNum;
$servicoSelecionado = filter_input(INPUT_GET, 'servico', FILTER_VALIDATE_INT);
$servicoIds = array_map('intval', array_column($servicos, 'id'));
if (!in_array($servicoSelecionado, $servicoIds, true)) {
    $servicoSelecionado = null;
}

ob_start();
?>

<!-- ── Hero Compacto ── -->
<section class="py-5" style="background:linear-gradient(135deg,#1a2a0a,#2D3E1F);">
    <div class="container text-center text-white py-2">
        <h1 class="display-5 fw-bold mb-2">Solicite seu Orçamento</h1>
        <p class="lead mb-0" style="color:rgba(255,255,255,.7);">
            Rápido, sem compromisso. Preencha e fale direto com a equipe.
        </p>
    </div>
</section>

<!-- ── Formulário de Lead ── -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <!-- Card do Formulário -->
                <div class="card border-0 shadow" style="border-radius:24px;">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:64px;height:64px;background:linear-gradient(135deg,#8FDC4F,#4A9B2E);border-radius:20px;font-size:1.8rem;color:#fff;">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h2 class="h4 fw-bold mb-1">Fale com a Equipe</h2>
                            <p class="text-muted small">
                                Preencha os dados para abrir uma mensagem pronta. Conclua o envio no WhatsApp.
                            </p>
                        </div>

                        <form id="formLead">
                            <!-- Nome -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="leadNome">
                                    <i class="bi bi-person me-1 text-success"></i>Seu nome *
                                </label>
                                <input type="text" class="form-control" id="leadNome"
                                       placeholder="Ex: Maria Silva"
                                       required autocomplete="name">
                                <div class="invalid-feedback">Por favor, informe seu nome.</div>
                            </div>

                            <!-- Nome do Pet -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="leadNomePet">
                                    <i class="bi bi-heart me-1 text-success"></i>Nome do seu pet (opcional)
                                </label>
                                <input type="text" class="form-control" id="leadNomePet"
                                       placeholder="Ex: Thor, Luna, Bolinha...">
                            </div>

                            <!-- Serviço -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="leadServico">
                                    <i class="bi bi-grid me-1 text-success"></i>Serviço desejado *
                                </label>
                                <select class="form-select" id="leadServico" required>
                                    <option value="" disabled <?= $servicoSelecionado === null ? 'selected' : '' ?>>Selecione o serviço...</option>
                                    <?php foreach ($servicos as $sv): ?>
                                    <option value="<?= safe($sv['nome']) ?>" <?= (int) $sv['id'] === $servicoSelecionado ? 'selected' : '' ?>>
                                        <?= safe($sv['nome']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <option value="Outros / Não sei">Outros / Não sei ainda</option>
                                </select>
                                <div class="invalid-feedback">Selecione um serviço.</div>
                            </div>

                            <!-- Whatsapp (opcional, campo informativo) -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="leadHorario">
                                    <i class="bi bi-telephone me-1 text-success"></i>Melhor horário para contato
                                    <span class="text-muted fw-normal">(opcional)</span>
                                </label>
                                <input type="text" class="form-control" id="leadHorario"
                                       placeholder="Ex: Manhãs, qualquer horário...">
                            </div>

                            <!-- Mensagem adicional -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold" for="leadMensagem">
                                    <i class="bi bi-chat-text me-1 text-success"></i>Informações do seu pet
                                    <span class="text-muted fw-normal">(opcional)</span>
                                </label>
                                <textarea class="form-control" id="leadMensagem" rows="3"
                                          placeholder="Raça, porte, idade, necessidades especiais..."></textarea>
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold"
                                    style="border-radius:50px; padding:14px;">
                                <i class="fab fa-whatsapp me-2 fs-5"></i>
                                Enviar pelo WhatsApp
                            </button>
                        </form>

                        <p class="text-center mt-3 mb-0">
                            <a href="<?= safe($whatsappBase) ?>">Conversar sem preencher o formulário</a>
                        </p>

                        <!-- Rodapé do card -->
                        <p class="text-center text-muted small mt-3 mb-0">
                            Você poderá revisar a mensagem antes de enviá-la.
                        </p>
                    </div>
                </div>

                <!-- Diferenciais abaixo do form -->
                <div class="row g-3 mt-3 text-center">
                    <div class="col-4">
                        <div class="p-3 rounded-3" style="background:#fff;">
                            <i class="fab fa-whatsapp" style="color:#6BBF2D; font-size:1.4rem;"></i>
                            <p class="small fw-semibold mb-0 mt-1">Atendimento<br>pelo WhatsApp</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-3" style="background:#fff;">
                            <i class="bi bi-shield-check" style="color:#6BBF2D; font-size:1.4rem;"></i>
                            <p class="small fw-semibold mb-0 mt-1">Equipe<br>Certificada</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-3" style="background:#fff;">
                            <i class="bi bi-geo-alt" style="color:#6BBF2D; font-size:1.4rem;"></i>
                            <p class="small fw-semibold mb-0 mt-1">Osasco e<br>Zona Oeste</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

ob_start();
?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const WHATSAPP_NUM = '<?= $whatsappNum ?>';
    const form = document.getElementById('formLead');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const nome     = document.getElementById('leadNome').value.trim();
        const nomePet  = document.getElementById('leadNomePet').value.trim();
        const servico  = document.getElementById('leadServico').value.trim();
        const horario  = document.getElementById('leadHorario').value.trim();
        const info     = document.getElementById('leadMensagem').value.trim();

        // Montar mensagem
        let msg = `Olá, equipe Curicas Pet! 🐾\n\n`;
        msg += `*Nome:* ${nome}\n`;
        if (nomePet) msg += `*Pet:* ${nomePet}\n`;
        msg += `*Serviço:* ${servico}\n`;
        if (horario) msg += `*Melhor horário:* ${horario}\n`;
        if (info)    msg += `*Informações do pet:* ${info}\n`;
        msg += `\nGostaria de solicitar um orçamento, por favor!`;

        window.location.assign(`https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(msg)}`);
    });
});
</script>
<?php
$scripts = ob_get_clean();

require __DIR__ . '/layouts/base.php';
?>