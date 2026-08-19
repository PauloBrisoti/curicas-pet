<?php
/**
 * ============================================================
 * CONFIG/APP.PHP — CONFIGURAÇÕES GLOBAIS DA APLICAÇÃO
 * ============================================================
 *
 * Centraliza dados sensíveis que NÃO devem ser hardcoded
 * em nenhuma View ou Controller.
 *
 * ⚠️ IMPORTANTE: Não versionar este arquivo em repositórios
 * públicos! Adicione ao .gitignore se usar Git.
 * ============================================================
 */

return [

    // ============================================================
    // PIX — Dados do recebedor (exibidos na seção de doação)
    // ============================================================

    // Chave PIX (e-mail, CPF, CNPJ, telefone ou chave aleatória)
    'pix_chave'   => 'contato@curicaspet.com.br',

    // Nome do titular exibido ao pagador (antifraude)
    'pix_titular' => 'Curicas Pet',

    // ============================================================
    // AMBIENTE
    // ============================================================

    // 'development' | 'production'
    'ambiente' => 'production',

    // ============================================================
    // MÓDULO DE DOAÇÃO — controlado pelo painel admin
    // ============================================================

    // true  = exibe a seção de doação/PIX na página de adoção
    // false = oculta a seção completamente
    'doacao_ativa' => true,

    // E-mail de contato para apoio à causa animal (opcional).
    // Quando preenchido, exibe link mailto: na seção de doação.
    // Deixe vazio '' para não exibir.
    'email_apoio' => '',

];
