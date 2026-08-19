<?php
/**
 * ============================================================
 * CONFIGURAÇÃO CENTRAL DO NEGÓCIO - CURICAS PET
 * ============================================================
 * 
 * INSTRUÇÕES:
 * Edite os valores abaixo com os dados REAIS da sua empresa.
 * Estes dados serão usados em:
 * - Botões de WhatsApp e Instagram
 * - Link "Como Chegar" (GPS)
 * - SEO e Schema.org (ajuda no Google Maps)
 * 
 * ============================================================
 */

return [
    // ============================================================
    // DADOS DA EMPRESA
    // ============================================================

    // Nome da empresa (aparece no site e no Google)
    'nome_empresa' => 'Curicas Pet',

    // Slogan/Tagline (usado em SEO)
    'slogan' => 'Cuidado Premium para seu Pet',

    // Descrição curta (usado em meta description)
    'descricao' => 'Serviços profissionais de Dog Walker e Pet Sitter em Osasco e região. Passeios e cuidados para seu pet com profissionais qualificados.',

    // ============================================================
    // CONTATO E REDES SOCIAIS
    // ============================================================

    // WhatsApp (APENAS NÚMEROS com código do país)
    // 55 = Brasil, 11 = DDD, 969440485 = número
    'whatsapp_num' => '5511969440485',

    // Mensagem padrão do WhatsApp
    'whatsapp_msg' => 'Olá! Gostaria de saber mais sobre os serviços da Curicas Pet.',

    // Instagram (URL completa do perfil)
    'instagram_url' => 'https://www.instagram.com/curicaspet',

    // Facebook (URL completa da página)
    'facebook_url' => 'https://www.facebook.com/Curicaspet',

    // Email de contato
    'email' => 'contato@curicaspet.com.br',

    // Telefone formatado para exibição
    'telefone_display' => '(11) 96944-0485',

    // ============================================================
    // ENDEREÇO E GEOLOCALIZAÇÃO
    // Coordenadas obtidas para: Praça Padroeira do Brasil, 111 - Centro, Osasco
    // ============================================================

    // Endereço completo (aparece no rodapé e Schema.org)
    'endereco_completo' => 'Praça Padroeira do Brasil, 111 - Centro, Osasco - SP',

    // CEP
    'cep' => '06010-090',

    // Cidade
    'cidade' => 'Osasco',

    // Estado (sigla)
    'estado' => 'SP',

    // País
    'pais' => 'Brasil',

    // Latitude (coordenadas GPS do endereço)
    'geo_latitude' => '-23.5349',

    // Longitude (coordenadas GPS do endereço)
    'geo_longitude' => '-46.77409',

    // ============================================================
    // HORÁRIO DE FUNCIONAMENTO
    // ============================================================

    'horario_funcionamento' => [
        'creche' => [
            'segunda_sexta' => '07:30 - 19:30',
            'sabado' => 'Fechado',
            'domingo' => 'Fechado'
        ],
        'hotel' => [
            'segunda_sabado' => '09:30 - 15:00',
            'domingo' => '10:00 - 14:00'
        ]
    ],

    // Formato para Schema.org (ISO 8601) - usando horário geral
    'horario_schema' => 'Mo-Fr 07:30-19:30, Sa 09:30-15:00, Su 10:00-14:00',

    // ============================================================
    // IMAGENS PARA SEO/REDES SOCIAIS
    // ============================================================

    // Imagem padrão para compartilhamento (Open Graph / Twitter Card)
    // Recomendado: 1200x630px — usar foto real do serviço principal
    'og_image' => 'https://curicaspet.com.br/assets/images/servicos/asilo-canino.webp',

    // Logo da empresa (para Schema.org)
    'logo_url' => 'https://curicaspet.com.br/assets/images/logo-curicas-pet.png',

    // ============================================================
    // URL DO SITE
    // ============================================================

    // URL base do site (SEM barra no final)
    // ALTERE PARA O DOMÍNIO REAL QUANDO SUBIR PARA PRODUÇÃO
    'site_url' => 'https://curicaspet.com.br',
];
