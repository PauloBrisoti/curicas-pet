<?php
/**
 * ============================================================
 * CONTROLLER: HOME
 * ============================================================
 * 
 * Gerencia a página inicial do site.
 * Renderiza serviços dinamicamente a partir do banco de dados.
 * 
 * MODO DEMO: Se o banco não estiver disponível, exibe dados
 * de demonstração para preview local.
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../models/Blog.php';

class HomeController
{
    /**
     * Exibe a página inicial
     * Lista todos os serviços ativos (ou dados demo se sem banco)
     */
    public function index(): void
    {
        // Verificar se há conexão com banco de dados
        if (Database::isConnected()) {
            // Modo produção: buscar do banco
            $servicoModel    = new Servico();
            $servicos        = $servicoModel->getAtivos();
            $blogModel       = new Blog();
            $artigosCluster  = $blogModel->listarAtivos(3);
        } else {
            // Modo demo: dados de demonstração
            $servicos       = $this->getDemoServicos();
            $artigosCluster = [];
        }

        // Carregar a view
        require __DIR__ . '/../views/home.php';
    }

    /**
     * Retorna dados de demonstração para visualização sem banco
     * Estes dados simulam o que estaria no banco de dados
     */
    private function getDemoServicos(): array
    {
        return [
            [
                'id' => 1,
                'nome' => 'Hospedagem Premium',
                'descricao' => 'Seu pet merece férias tão boas quanto as suas! Oferecemos acomodações individuais climatizadas, alimentação premium, passeios diários e monitoramento 24 horas.',
                'preco_base' => 120.00,
                'icone_bootstrap' => 'bi-house-heart',
                'imagem_url' => 'https://placehold.co/600x400/8FDC4F/white?text=Hotel+Pet',
                'tipo_calculo' => 'diaria'
            ],
            [
                'id' => 2,
                'nome' => 'Dog Walking Profissional',
                'descricao' => 'Passeios seguros e divertidos com condutores experientes! Rotas em parques arborizados, grupos pequenos e acompanhamento via GPS em tempo real.',
                'preco_base' => 45.00,
                'icone_bootstrap' => 'bi-signpost-split',
                'imagem_url' => 'https://placehold.co/600x400/6BBF2D/white?text=Dog+Walking',
                'tipo_calculo' => 'unidade'
            ],
            [
                'id' => 3,
                'nome' => 'Pet Sitter em Casa',
                'descricao' => 'O conforto da sua casa com cuidado profissional! Nosso Pet Sitter vai até você para alimentação, brincadeiras e companhia.',
                'preco_base' => 80.00,
                'icone_bootstrap' => 'bi-person-heart',
                'imagem_url' => 'https://placehold.co/600x400/4A9B2E/white?text=Pet+Sitter',
                'tipo_calculo' => 'hora'
            ]
        ];
    }
}
