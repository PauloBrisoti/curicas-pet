<?php
/**
 * ============================================================
 * VIEW: POLÍTICA DE PRIVACIDADE (LGPD)
 * ============================================================
 * 
 * Página de política de privacidade em conformidade com a
 * Lei Geral de Proteção de Dados (LGPD - Lei 13.709/2018)
 * ============================================================
 */

$pageTitle = 'Política de Privacidade - Curicas Pet';
$pageDescription = 'Conheça nossa política de privacidade e como tratamos seus dados pessoais em conformidade com a LGPD.';

// Carregar configurações do negócio
$businessConfig = require __DIR__ . '/../config/business.php';

ob_start();
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #8FDC4F, #6BBF2D);">
    <div class="container text-center text-white">
        <h1 class="display-5 fw-bold"><i class="bi bi-shield-lock me-2"></i>Política de Privacidade</h1>
        <p class="lead mb-0">Transparência no tratamento dos seus dados</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-body p-4 p-lg-5">

                        <p class="text-muted mb-4">
                            <strong>Última atualização:</strong>
                            <?= date('d/m/Y') ?>
                        </p>

                        <!-- 1. Introdução -->
                        <h2 class="h4 fw-bold mt-4 mb-3">1. Introdução</h2>
                        <p>
                            A <strong>
                                <?= safe($businessConfig['nome_empresa']) ?>
                            </strong> ("nós", "nosso" ou "Empresa"),
                            pessoa jurídica de direito privado, com sede em
                            <?= safe($businessConfig['endereco_completo']) ?>,
                            está comprometida em proteger a privacidade e os dados pessoais de seus clientes,
                            usuários e visitantes do site.
                        </p>
                        <p>
                            Esta Política de Privacidade foi elaborada em conformidade com a
                            <strong>Lei Geral de Proteção de Dados Pessoais (LGPD - Lei nº 13.709/2018)</strong>
                            e tem como objetivo informar como coletamos, usamos, armazenamos e protegemos seus dados
                            pessoais.
                        </p>

                        <!-- 2. Dados Coletados -->
                        <h2 class="h4 fw-bold mt-5 mb-3">2. Dados Pessoais Coletados</h2>
                        <p>Podemos coletar os seguintes dados pessoais:</p>
                        <ul>
                            <li><strong>Dados de identificação:</strong> nome completo, CPF (quando aplicável)</li>
                            <li><strong>Dados de contato:</strong> e-mail, telefone, endereço</li>
                            <li><strong>Dados do pet:</strong> nome, raça, idade, características, fotos</li>
                            <li><strong>Dados de navegação:</strong> endereço IP, tipo de navegador, páginas acessadas,
                                data e hora de acesso</li>
                            <li><strong>Dados de comunicação:</strong> mensagens enviadas por WhatsApp, formulários ou
                                e-mail</li>
                        </ul>

                        <!-- 3. Finalidade -->
                        <h2 class="h4 fw-bold mt-5 mb-3">3. Finalidade do Tratamento</h2>
                        <p>Utilizamos seus dados pessoais para as seguintes finalidades:</p>
                        <ul>
                            <li>Prestação de nossos serviços (hospedagem, passeios, pet sitter)</li>
                            <li>Comunicação sobre agendamentos, orçamentos e atendimentos</li>
                            <li>Envio de informações e novidades sobre nossos serviços (com seu consentimento)</li>
                            <li>Processo de adoção responsável de pets</li>
                            <li>Cumprimento de obrigações legais e regulatórias</li>
                            <li>Melhoria contínua de nossos serviços e experiência do usuário</li>
                        </ul>

                        <!-- 4. Base Legal -->
                        <h2 class="h4 fw-bold mt-5 mb-3">4. Base Legal para o Tratamento</h2>
                        <p>O tratamento de dados pessoais é realizado com base nas seguintes hipóteses legais previstas
                            na LGPD:</p>
                        <ul>
                            <li><strong>Consentimento:</strong> quando você fornece seus dados voluntariamente</li>
                            <li><strong>Execução de contrato:</strong> para prestação dos serviços contratados</li>
                            <li><strong>Interesse legítimo:</strong> para melhorar nossos serviços e comunicação</li>
                            <li><strong>Cumprimento de obrigação legal:</strong> quando exigido por lei</li>
                        </ul>

                        <!-- 5. Compartilhamento -->
                        <h2 class="h4 fw-bold mt-5 mb-3">5. Compartilhamento de Dados</h2>
                        <p>
                            Seus dados pessoais <strong>não são vendidos</strong> a terceiros.
                            Podemos compartilhar dados apenas nas seguintes situações:
                        </p>
                        <ul>
                            <li>Com prestadores de serviços essenciais (ex: plataformas de pagamento)</li>
                            <li>Para cumprimento de obrigações legais ou ordem judicial</li>
                            <li>Com seu consentimento expresso para finalidades específicas</li>
                        </ul>

                        <!-- 6. Armazenamento e Segurança -->
                        <h2 class="h4 fw-bold mt-5 mb-3">6. Armazenamento e Segurança</h2>
                        <p>
                            Adotamos medidas técnicas e organizacionais adequadas para proteger seus dados pessoais
                            contra acesso não autorizado, perda, alteração ou destruição. Seus dados são armazenados
                            em servidores seguros e mantidos apenas pelo tempo necessário para as finalidades descritas.
                        </p>

                        <!-- 7. Direitos do Titular -->
                        <h2 class="h4 fw-bold mt-5 mb-3">7. Seus Direitos (Art. 18 da LGPD)</h2>
                        <p>Você tem os seguintes direitos em relação aos seus dados pessoais:</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <ul>
                                    <li>Confirmação da existência de tratamento</li>
                                    <li>Acesso aos dados</li>
                                    <li>Correção de dados incompletos ou desatualizados</li>
                                    <li>Anonimização, bloqueio ou eliminação de dados desnecessários</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li>Portabilidade dos dados</li>
                                    <li>Eliminação dos dados tratados com consentimento</li>
                                    <li>Informação sobre compartilhamento</li>
                                    <li>Revogação do consentimento</li>
                                </ul>
                            </div>
                        </div>

                        <!-- 8. Cookies -->
                        <h2 class="h4 fw-bold mt-5 mb-3">8. Cookies e Tecnologias Similares</h2>
                        <p>
                            Nosso site pode utilizar cookies para melhorar sua experiência de navegação.
                            Cookies são pequenos arquivos armazenados em seu dispositivo que nos ajudam a:
                        </p>
                        <ul>
                            <li>Lembrar suas preferências</li>
                            <li>Analisar o tráfego do site</li>
                            <li>Personalizar conteúdo</li>
                        </ul>
                        <p>Você pode configurar seu navegador para recusar cookies, mas isso pode afetar algumas
                            funcionalidades do site.</p>

                        <!-- 9. Contato -->
                        <h2 class="h4 fw-bold mt-5 mb-3">9. Contato e Encarregado de Dados</h2>
                        <p>
                            Para exercer seus direitos ou esclarecer dúvidas sobre esta política,
                            entre em contato conosco:
                        </p>
                        <div class="bg-light rounded-3 p-4 mt-3">
                            <p class="mb-2"><strong>
                                    <?= safe($businessConfig['nome_empresa']) ?>
                                </strong></p>
                            <p class="mb-2"><i class="bi bi-envelope me-2"></i>
                                <?= safe($businessConfig['email']) ?>
                            </p>
                            <p class="mb-2"><i class="bi bi-telephone me-2"></i>
                                <?= safe($businessConfig['telefone_display']) ?>
                            </p>
                            <p class="mb-0"><i class="bi bi-geo-alt me-2"></i>
                                <?= safe($businessConfig['endereco_completo']) ?>
                            </p>
                        </div>

                        <!-- 10. Atualizações -->
                        <h2 class="h4 fw-bold mt-5 mb-3">10. Atualizações desta Política</h2>
                        <p>
                            Esta Política de Privacidade pode ser atualizada periodicamente.
                            Recomendamos que você a consulte regularmente.
                            A data da última atualização estará sempre indicada no início deste documento.
                        </p>

                        <hr class="my-5">

                        <div class="text-center">
                            <p class="text-muted small mb-3">
                                Ao utilizar nossos serviços, você concorda com esta Política de Privacidade.
                            </p>
                            <a href="index.php" class="btn btn-primary">
                                <i class="bi bi-arrow-left me-2"></i>Voltar para o Site
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/base.php';
?>