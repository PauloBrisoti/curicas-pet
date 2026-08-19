<?php
/**
 * ============================================================
 * ATALHO DE AVALIAÇÃO — Google Meu Negócio
 * ============================================================
 * Centraliza o link de avaliação no nosso domínio para garantir
 * que materiais impressos (QR Codes, cartões) nunca quebrem,
 * mesmo que o Google mude a estrutura de URLs no futuro.
 *
 * Para atualizar a URL de destino, edite APENAS a constante
 * GOOGLE_REVIEW_URL abaixo — o resto do código permanece intacto.
 *
 * URL pública: curicaspet.com.br/avaliar
 * ============================================================
 */

// ──────────────────────────────────────────────────────────────
// ⬇️  COLE AQUI O LINK OFICIAL DO GOOGLE MEU NEGÓCIO
//     (gerado em: business.google.com → Pedir avaliações)
// ──────────────────────────────────────────────────────────────
define('GOOGLE_REVIEW_URL', 'COLE_AQUI_O_LINK_DO_GOOGLE');
// ──────────────────────────────────────────────────────────────

// Valida que o link foi configurado
if (GOOGLE_REVIEW_URL === 'COLE_AQUI_O_LINK_DO_GOOGLE' || empty(GOOGLE_REVIEW_URL)) {
    // Em homologação: exibe aviso amigável em vez de redirecionar para nada
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
          <title>Link não configurado | Curicas Pet</title></head><body style="font-family:sans-serif;padding:2rem;">
          <h1>⚙️ Configure o link de avaliação</h1>
          <p>Edite o arquivo <code>views/avaliar.php</code> e substitua
          <code>COLE_AQUI_O_LINK_DO_GOOGLE</code> pela URL real do Google Meu Negócio.</p>
          </body></html>';
    exit;
}

// Redirect 301 permanente → URL do Google Meu Negócio
header('Location: ' . GOOGLE_REVIEW_URL, true, 301);
exit;
