<?php
/**
 * ============================================================
 * CONTROLLER: ORÇAMENTO
 * ============================================================
 * 
 * Gerencia a calculadora de orçamento.
 * 
 * LÓGICA DE CÁLCULO:
 * - Hotel (diaria): dias × preço × quantidade_pets
 * - Walking (unidade): passeios × preço × quantidade_pets
 * - Pet Sitter (hora): horas × preço × quantidade_pets
 * 
 * DESCONTO:
 * - 10% automático para 2 ou mais pets
 * 
 * MODO DEMO: Funciona com dados de demonstração quando
 * o banco de dados não está disponível.
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../models/Cotacao.php';

class OrcamentoController
{
    // Taxa de desconto para 2+ pets (10%)
    private const DESCONTO_MULTIPLOS_PETS = 0.10;

    /**
     * Exibe o formulário de orçamento
     */
    public function index(): void
    {
        // Verificar se há conexão com banco de dados
        if (Database::isConnected()) {
            $servicoModel = new Servico();
            $servicos = $servicoModel->getAtivos();
        } else {
            // Modo demo
            $servicos = $this->getDemoServicos();
        }

        $errors = [];
        $success = false;
        $flash = getFlashMessage();

        require __DIR__ . '/../views/orcamento.php';
    }

    /**
     * Processa o formulário de orçamento via AJAX
     * Retorna JSON com o cálculo
     */
    public function calcular(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['error' => 'Método não permitido'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $servicosSelecionados = $input['servicos'] ?? [];
        $quantidadePets = sanitize($input['quantidade_pets'] ?? 1, 'int') ?: 1;

        if (empty($servicosSelecionados)) {
            jsonResponse(['error' => 'Selecione pelo menos um serviço'], 400);
        }

        // Buscar serviços (do banco ou demo)
        if (Database::isConnected()) {
            $servicoModel = new Servico();
            $servicosIds = array_column($servicosSelecionados, 'id');
            $servicos = $servicoModel->getByIds($servicosIds);
        } else {
            // Modo demo: filtrar apenas os selecionados
            $servicosIds = array_column($servicosSelecionados, 'id');
            $allServicos = $this->getDemoServicos();
            $servicos = array_filter($allServicos, fn($s) => in_array($s['id'], $servicosIds));
        }

        // Indexar por ID para fácil acesso
        $servicosIndexados = [];
        foreach ($servicos as $servico) {
            $servicosIndexados[$servico['id']] = $servico;
        }

        // Calcular valor
        $valorTotal = 0;
        $detalhes = [];

        foreach ($servicosSelecionados as $item) {
            $servicoId = sanitize($item['id'], 'int');
            $quantidade = sanitize($item['quantidade'], 'int') ?: 1;

            if (!isset($servicosIndexados[$servicoId])) {
                continue;
            }

            $servico = $servicosIndexados[$servicoId];
            $precoBase = (float) $servico['preco_base'];
            $subtotal = $precoBase * $quantidade * $quantidadePets;
            $valorTotal += $subtotal;

            $detalhes[] = [
                'servico_id' => $servicoId,
                'nome' => $servico['nome'],
                'tipo_calculo' => $servico['tipo_calculo'],
                'preco_unitario' => $precoBase,
                'quantidade' => $quantidade,
                'subtotal' => $subtotal
            ];
        }

        // Aplicar desconto para 2+ pets
        $desconto = 0;
        if ($quantidadePets >= 2) {
            $desconto = $valorTotal * self::DESCONTO_MULTIPLOS_PETS;
        }

        $valorFinal = $valorTotal - $desconto;

        jsonResponse([
            'success' => true,
            'detalhes' => $detalhes,
            'quantidade_pets' => $quantidadePets,
            'valor_estimado' => round($valorTotal, 2),
            'desconto' => round($desconto, 2),
            'percentual_desconto' => $quantidadePets >= 2 ? '10%' : '0%',
            'valor_final' => round($valorFinal, 2)
        ]);
    }

    /**
     * Salva a cotação no banco de dados
     */
    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['error' => 'Método não permitido'], 405);
        }

        // Verificar conexão com banco - modo demo não salva
        if (!Database::isConnected()) {
            jsonResponse([
                'success' => true,
                'message' => '⚠️ MODO DEMO: Orçamento simulado! Em produção, será salvo no banco de dados.',
                'cotacao_id' => 999,
                'demo_mode' => true
            ]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        // Validar campos obrigatórios
        $nome = sanitize($input['nome'] ?? '', 'string');
        $email = sanitize($input['email'] ?? '', 'email');
        $telefone = sanitize($input['telefone'] ?? '', 'phone');
        $servicos = $input['servicos'] ?? [];
        $quantidadePets = sanitize($input['quantidade_pets'] ?? 1, 'int') ?: 1;
        $valorEstimado = sanitize($input['valor_estimado'] ?? 0, 'float') ?: 0;
        $desconto = sanitize($input['desconto'] ?? 0, 'float') ?: 0;
        $valorFinal = sanitize($input['valor_final'] ?? 0, 'float') ?: 0;
        $mensagem = sanitize($input['mensagem'] ?? '', 'string');

        $errors = [];
        if (empty($nome)) {
            $errors[] = 'Nome é obrigatório';
        }
        if (empty($email)) {
            $errors[] = 'Email inválido';
        }
        if (empty($servicos)) {
            $errors[] = 'Selecione pelo menos um serviço';
        }

        if (!empty($errors)) {
            jsonResponse(['error' => implode(', ', $errors)], 400);
        }

        try {
            $cotacaoModel = new Cotacao();
            $cotacaoId = $cotacaoModel->create([
                'nome_cliente' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'servicos_selecionados' => $servicos,
                'quantidade_pets' => $quantidadePets,
                'valor_estimado' => $valorEstimado,
                'desconto_aplicado' => $desconto,
                'valor_final' => $valorFinal,
                'mensagem' => $mensagem
            ]);

            // Enviar email de notificação via SMTP
            $this->enviarEmailNotificacaoSmtp([
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'servicos' => $servicos,
                'quantidade_pets' => $quantidadePets,
                'valor_final' => $valorFinal,
                'mensagem' => $mensagem,
                'cotacao_id' => $cotacaoId
            ]);

            jsonResponse([
                'success' => true,
                'message' => 'Orçamento enviado com sucesso! Entraremos em contato em breve.',
                'cotacao_id' => $cotacaoId
            ]);

        } catch (Exception $e) {
            error_log('Erro ao salvar cotação: ' . $e->getMessage());
            jsonResponse(['error' => 'Erro ao processar solicitação. Tente novamente.'], 500);
        }
    }

    /**
     * Retorna dados de demonstração para visualização sem banco
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

    /**
     * Envia email de notificação quando uma cotação é recebida
     */
    private function enviarEmailNotificacao(array $dados): void
    {
        try {
            // Carregar configurações do negócio
            $businessConfig = require __DIR__ . '/../config/business.php';
            $emailDestino = $businessConfig['email'] ?? 'contato@curicaspet.com.br';
            $nomeEmpresa = $businessConfig['nome_empresa'] ?? 'Curicas Pet';

            // Formatar lista de serviços
            $servicosTexto = '';
            if (is_array($dados['servicos'])) {
                foreach ($dados['servicos'] as $servico) {
                    $servicosTexto .= "- " . ($servico['nome'] ?? 'Serviço') . " (Qtd: " . ($servico['quantidade'] ?? 1) . ")\n";
                }
            }

            // Formatar telefone para WhatsApp (remover caracteres não numéricos)
            $telefoneWhatsApp = preg_replace('/\D/', '', $dados['telefone']);
            // Adicionar código do Brasil se não tiver
            if (strlen($telefoneWhatsApp) <= 11) {
                $telefoneWhatsApp = '55' . $telefoneWhatsApp;
            }
            $mensagemWhatsApp = urlencode("Olá {$dados['nome']}! Recebemos sua solicitação de orçamento na Curicas Pet. Vamos conversar sobre os serviços?");
            $linkWhatsApp = "https://wa.me/{$telefoneWhatsApp}?text={$mensagemWhatsApp}";

            // Montar corpo do email
            $assunto = "Nova Solicitação de Orçamento #" . $dados['cotacao_id'];

            $corpo = "
====================================================
NOVA SOLICITAÇÃO DE ORÇAMENTO - {$nomeEmpresa}
====================================================

DADOS DO CLIENTE:
-----------------
Nome: {$dados['nome']}
Email: {$dados['email']}
Telefone: {$dados['telefone']}

>> RESPONDER VIA WHATSAPP (clique ou copie):
{$linkWhatsApp}

SERVIÇOS SOLICITADOS:
---------------------
{$servicosTexto}
Quantidade de Pets: {$dados['quantidade_pets']}

VALOR ESTIMADO: R$ " . number_format($dados['valor_final'], 2, ',', '.') . "

MENSAGEM DO CLIENTE:
--------------------
{$dados['mensagem']}

====================================================
Cotação ID: #{$dados['cotacao_id']}
Data: " . date('d/m/Y H:i') . "
====================================================

Acesse o painel administrativo para mais detalhes:
https://curicaspet.com.br/index.php?page=admin
";

            // Cabeçalhos do email (melhorados para evitar spam)
            $headers = [];
            $headers[] = "From: Curicas Pet <contato@curicaspet.com.br>";
            $headers[] = "Reply-To: {$dados['email']}";
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
            $headers[] = "X-Mailer: PHP/" . phpversion();
            $headers[] = "X-Priority: 1";
            $headersStr = implode("\r\n", $headers);

            // Enviar email
            $enviado = mail($emailDestino, $assunto, $corpo, $headersStr);

            if (!$enviado) {
                error_log("Falha ao enviar email de notificação para cotação #{$dados['cotacao_id']}");
            }

        } catch (Exception $e) {
            // Não interromper o fluxo se o email falhar
            error_log("Erro ao enviar email de notificação: " . $e->getMessage());
        }
    }

    /**
     * Envia email de notificação via SMTP (Hostinger)
     */
    private function enviarEmailNotificacaoSmtp(array $dados): void
    {
        try {
            // Formatar lista de serviços
            $servicosTexto = '';
            if (is_array($dados['servicos'])) {
                foreach ($dados['servicos'] as $servico) {
                    $servicosTexto .= "- " . ($servico['nome'] ?? 'Serviço') . " (Qtd: " . ($servico['quantidade'] ?? 1) . ")\n";
                }
            }

            // Formatar telefone para WhatsApp
            $telefoneWhatsApp = preg_replace('/\D/', '', $dados['telefone']);
            if (strlen($telefoneWhatsApp) <= 11) {
                $telefoneWhatsApp = '55' . $telefoneWhatsApp;
            }
            $mensagemWhatsApp = urlencode("Olá {$dados['nome']}! Recebemos sua solicitação de orçamento na Curicas Pet. Vamos conversar sobre os serviços?");
            $linkWhatsApp = "https://wa.me/{$telefoneWhatsApp}?text={$mensagemWhatsApp}";

            // Montar corpo do email
            $assunto = "Nova Cotação #{$dados['cotacao_id']} - {$dados['nome']}";

            $corpo = "
🐕 NOVA SOLICITAÇÃO DE ORÇAMENTO
================================

CLIENTE:
Nome: {$dados['nome']}
Email: {$dados['email']}
Telefone: {$dados['telefone']}

📱 RESPONDER VIA WHATSAPP:
{$linkWhatsApp}

SERVIÇOS:
{$servicosTexto}
Pets: {$dados['quantidade_pets']}

💰 VALOR: R$ " . number_format($dados['valor_final'], 2, ',', '.') . "

MENSAGEM:
{$dados['mensagem']}

---
Cotação #{$dados['cotacao_id']} | " . date('d/m/Y H:i') . "
Painel: https://curicaspet.com.br/index.php?page=admin
";

            // Enviar via SMTP
            $enviado = sendSmtpEmail(
                SMTP_FROM_EMAIL, // Envia para si mesmo
                $assunto,
                $corpo,
                $dados['email'] // Reply-To do cliente
            );

            if ($enviado) {
                error_log("Email SMTP enviado com sucesso para cotação #{$dados['cotacao_id']}");
            } else {
                error_log("Falha ao enviar email SMTP para cotação #{$dados['cotacao_id']}");
            }

        } catch (Exception $e) {
            error_log("Erro SMTP: " . $e->getMessage());
        }
    }
}
