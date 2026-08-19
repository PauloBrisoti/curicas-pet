<?php
/**
 * ============================================================
 * CONTROLLER: ADMIN
 * ============================================================
 * 
 * Gerencia o painel administrativo.
 * 
 * FUNCIONALIDADES:
 * - Login/Logout com autenticação
 * - Dashboard com estatísticas
 * - CRUD de serviços
 * - Visualização de cotações
 * 
 * SEGURANÇA:
 * - Todas as ações (exceto login) requerem autenticação
 * - Formulários protegidos com CSRF tokens
 * ============================================================
 */

require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../models/Cotacao.php';
require_once __DIR__ . '/../models/Pet.php';
require_once __DIR__ . '/../models/Blog.php';
require_once __DIR__ . '/../models/Gallery.php';

class AdminController
{
    private Admin $adminModel;
    private Servico $servicoModel;
    private Cotacao $cotacaoModel;
    private Pet $petModel;
    private Blog $blogModel;
    private Gallery $galleryModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
        $this->servicoModel = new Servico();
        $this->cotacaoModel = new Cotacao();
        $this->petModel = new Pet();
        $this->blogModel = new Blog();
        $this->galleryModel = new Gallery();
    }

    // ============================================================
    // AUTENTICAÇÃO
    // ============================================================

    /**
     * Exibe formulário de login
     */
    public function login(): void
    {
        // Se já está logado, ir direto ao dashboard
        if (isAdminLoggedIn()) {
            redirect('/?page=admin&action=dashboard');
        }

        $error = null;
        $flash = getFlashMessage();

        // Processar login (apenas POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email'] ?? '', 'email');
            $senha = $_POST['senha'] ?? '';

            if (!$email || !$senha) {
                $error = 'Preencha todos os campos';
            } else {
                $admin = $this->adminModel->autenticar($email, $senha);

                if ($admin) {
                    // ── Login válido ──────────────────────────────────
                    $_SESSION['admin_id']    = $admin['id'];
                    $_SESSION['admin_nome']  = $admin['nome'];
                    $_SESSION['admin_email'] = $admin['email'];

                    // NÃO chamar session_write_close() aqui.
                    // redirect() já faz isso depois de gravar o flash.
                    // Fazer antes causava: flash perdido + admin_id salvo
                    // mas sem mensagem de "Bem-vindo" e possível instabilidade.
                    session_regenerate_id(false);

                    // ▼ TESTE TEMPORÁRIO — remover após confirmar que a sessão está correta
                    // Se a página travar com os dados abaixo, sessão está OK — só o redirect falha.
                    // Se não aparecer, o problema é no banco/autenticação.
                    // die('<pre>LOGIN OK. Sessão: ' . print_r($_SESSION, true) . '</pre>');

                    redirect('/?page=admin&action=dashboard', 'Bem-vindo(a), ' . $admin['nome'] . '!', 'success');
                } else {
                    $error = 'Email ou senha inválidos';
                }
            }
        }

        require __DIR__ . '/../views/admin/login.php';
    }

    /**
     * Logout do admin
     */
    public function logout(): void
    {
        // Destruir sessão
        $_SESSION = [];
        session_destroy();

        // Redirecionar para login
        redirect('index.php?page=admin&action=login', 'Você foi desconectado.', 'info');
    }

    // ============================================================
    // DASHBOARD
    // ============================================================

    /**
     * Exibe o dashboard com estatísticas
     */
    public function dashboard(): void
    {
        requireAdminAuth();

        $flash = getFlashMessage();

        // Estatísticas
        $stats = [
            'total_servicos' => $this->servicoModel->countAtivos(),
            'cotacoes_por_status' => $this->cotacaoModel->countByStatus(),
            'total_cotacoes' => $this->cotacaoModel->countAll(),
            'valor_convertido' => $this->cotacaoModel->sumConvertidas()
        ];

        // Cotações recentes
        $cotacoesRecentes = $this->cotacaoModel->getRecentes(5);

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    // ============================================================
    // CRUD DE SERVIÇOS
    // ============================================================

    /**
     * Lista todos os serviços
     */
    public function servicos(): void
    {
        requireAdminAuth();

        $flash = getFlashMessage();
        $servicos = $this->servicoModel->getAll();

        require __DIR__ . '/../views/admin/servicos.php';
    }

    /**
     * Exibe formulário de criação de serviço
     */
    public function servicoNovo(): void
    {
        requireAdminAuth();

        $servico = null;
        $errors = [];
        $isEdit = false;

        require __DIR__ . '/../views/admin/servico_form.php';
    }

    /**
     * Exibe formulário de edição de serviço
     */
    public function servicoEditar(): void
    {
        requireAdminAuth();

        $id = sanitize($_GET['id'] ?? 0, 'int');

        if (!$id) {
            redirect('index.php?page=admin&action=servicos', 'Serviço não encontrado.', 'error');
        }

        $servico = $this->servicoModel->getById($id);

        if (!$servico) {
            redirect('index.php?page=admin&action=servicos', 'Serviço não encontrado.', 'error');
        }

        $errors = [];
        $isEdit = true;

        require __DIR__ . '/../views/admin/servico_form.php';
    }

    /**
     * Processa criação/edição de serviço
     */
    public function servicoSalvar(): void
    {
        requireAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=servicos');
        }

        // Validar CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? null)) {
            redirect('index.php?page=admin&action=servicos', 'Token de segurança inválido.', 'error');
        }

        // Coletar dados
        $id = sanitize($_POST['id'] ?? 0, 'int');
        $data = [
            'nome' => sanitize($_POST['nome'] ?? '', 'string'),
            'descricao' => sanitize($_POST['descricao'] ?? '', 'string'),
            'preco_base' => sanitize($_POST['preco_base'] ?? 0, 'float') ?: 0,
            'icone_bootstrap' => sanitize($_POST['icone_bootstrap'] ?? 'bi-star', 'string'),
            'imagem_url' => sanitize($_POST['imagem_url'] ?? '', 'string') ?: null,
            'tipo_calculo' => in_array($_POST['tipo_calculo'] ?? '', ['diaria', 'unidade', 'hora'])
                ? $_POST['tipo_calculo']
                : 'diaria',
            'ativo'         => isset($_POST['ativo']) ? 1 : 0,
            'ocultar_preco' => isset($_POST['ocultar_preco']) ? 1 : 0
        ];

        // Validações
        $errors = [];

        if (empty($data['nome'])) {
            $errors[] = 'Nome é obrigatório';
        } elseif ($this->servicoModel->existsByNome($data['nome'], $id ?: null)) {
            $errors[] = 'Já existe um serviço com este nome';
        }

        if (empty($data['descricao'])) {
            $errors[] = 'Descrição é obrigatória';
        }

        if ($data['preco_base'] <= 0) {
            $errors[] = 'Preço deve ser maior que zero';
        }

        if (!empty($errors)) {
            $servico = $data;
            $servico['id'] = $id;
            $isEdit = $id > 0;
            require __DIR__ . '/../views/admin/servico_form.php';
            return;
        }

        // Salvar
        try {
            if ($id > 0) {
                $this->servicoModel->update($id, $data);
                $message = 'Serviço atualizado com sucesso!';
            } else {
                $this->servicoModel->create($data);
                $message = 'Serviço criado com sucesso!';
            }

            regenerateCSRFToken();
            redirect('index.php?page=admin&action=servicos', $message, 'success');

        } catch (Exception $e) {
            error_log('Erro ao salvar serviço: ' . $e->getMessage());
            redirect('index.php?page=admin&action=servicos', 'Erro ao salvar serviço.', 'error');
        }
    }

    /**
     * Toggle ativo/inativo de serviço
     * SEGURO: exige método POST e token CSRF válido.
     */
    public function servicoToggle(): void
    {
        requireAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=servicos');
        }

        if (!validateCSRFToken($_POST['csrf_token'] ?? null)) {
            redirect('index.php?page=admin&action=servicos', 'Token de segurança inválido.', 'error');
        }

        $id = sanitize($_POST['id'] ?? 0, 'int');

        if (!$id) {
            redirect('index.php?page=admin&action=servicos', 'Serviço não encontrado.', 'error');
        }

        try {
            $this->servicoModel->toggleAtivo($id);
            regenerateCSRFToken();
            redirect('index.php?page=admin&action=servicos', 'Status do serviço alterado!', 'success');
        } catch (Exception $e) {
            redirect('index.php?page=admin&action=servicos', 'Erro ao alterar status.', 'error');
        }
    }

    /**
     * Toggle ocultar_preco de um serviço
     * SEGURO: exige método POST e token CSRF válido.
     */
    public function servicoToggleOcultar(): void
    {
        requireAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=servicos');
        }

        if (!validateCSRFToken($_POST['csrf_token'] ?? null)) {
            redirect('index.php?page=admin&action=servicos', 'Token de segurança inválido.', 'error');
        }

        $id = sanitize($_POST['id'] ?? 0, 'int');

        if (!$id) {
            redirect('index.php?page=admin&action=servicos', 'Serviço não encontrado.', 'error');
        }

        try {
            $this->servicoModel->toggleOcultarPreco($id);
            regenerateCSRFToken();
            redirect('index.php?page=admin&action=servicos', 'Visibilidade de preço alterada!', 'success');
        } catch (Exception $e) {
            redirect('index.php?page=admin&action=servicos', 'Erro ao alterar visibilidade.', 'error');
        }
    }

    /**
     * Exclui um serviço
     */
    public function servicoExcluir(): void
    {
        requireAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=servicos');
        }

        // Validar CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? null)) {
            redirect('index.php?page=admin&action=servicos', 'Token de segurança inválido.', 'error');
        }

        $id = sanitize($_POST['id'] ?? 0, 'int');

        if (!$id) {
            redirect('index.php?page=admin&action=servicos', 'Serviço não encontrado.', 'error');
        }

        try {
            $this->servicoModel->delete($id);
            regenerateCSRFToken();
            redirect('index.php?page=admin&action=servicos', 'Serviço excluído com sucesso!', 'success');
        } catch (Exception $e) {
            redirect('index.php?page=admin&action=servicos', 'Erro ao excluir serviço.', 'error');
        }
    }

    // ============================================================
    // COTAÇÕES
    // ============================================================

    /**
     * Lista todas as cotações
     */
    public function cotacoes(): void
    {
        requireAdminAuth();

        $flash = getFlashMessage();
        $statusFiltro = $_GET['status'] ?? null;

        if ($statusFiltro && in_array($statusFiltro, ['pendente', 'respondida', 'convertida', 'cancelada'])) {
            $cotacoes = $this->cotacaoModel->getByStatus($statusFiltro);
        } else {
            $cotacoes = $this->cotacaoModel->getAll();
        }

        $contadores = $this->cotacaoModel->countByStatus();

        require __DIR__ . '/../views/admin/cotacoes.php';
    }

    /**
     * Exibe detalhes de uma cotação
     */
    public function cotacaoDetalhe(): void
    {
        requireAdminAuth();

        $id = sanitize($_GET['id'] ?? 0, 'int');

        if (!$id) {
            redirect('index.php?page=admin&action=cotacoes', 'Cotação não encontrada.', 'error');
        }

        $cotacao = $this->cotacaoModel->getById($id);

        if (!$cotacao) {
            redirect('index.php?page=admin&action=cotacoes', 'Cotação não encontrada.', 'error');
        }

        require __DIR__ . '/../views/admin/cotacao_detalhe.php';
    }

    /**
     * Atualiza status de uma cotação
     */
    public function cotacaoStatus(): void
    {
        requireAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=cotacoes');
        }

        // Validar CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? null)) {
            redirect('index.php?page=admin&action=cotacoes', 'Token de segurança inválido.', 'error');
        }

        $id = sanitize($_POST['id'] ?? 0, 'int');
        $status = $_POST['status'] ?? '';

        if (!$id || !in_array($status, ['pendente', 'respondida', 'convertida', 'cancelada'])) {
            redirect('index.php?page=admin&action=cotacoes', 'Dados inválidos.', 'error');
        }

        try {
            $this->cotacaoModel->updateStatus($id, $status);
            regenerateCSRFToken();
            redirect('index.php?page=admin&action=cotacoes', 'Status atualizado!', 'success');
        } catch (Exception $e) {
            redirect('index.php?page=admin&action=cotacoes', 'Erro ao atualizar status.', 'error');
        }
    }

    // ============================================================
    // CRUD DE PETS PARA ADOÇÃO
    // ============================================================

    /**
     * Lista todos os pets
     */
    public function pets(): void
    {
        requireAdminAuth();

        $pets = $this->petModel->listarTodos();
        $contagem = $this->petModel->contarPorStatus();

        require __DIR__ . '/../views/admin/pets.php';
    }

    /**
     * Exibe formulário de criação de pet
     */
    public function petNovo(): void
    {
        requireAdminAuth();

        $pet = null;
        $erro = null;

        require __DIR__ . '/../views/admin/pet_form.php';
    }

    /**
     * Exibe formulário de edição de pet
     */
    public function petEditar(): void
    {
        requireAdminAuth();

        $id = sanitize($_GET['id'] ?? 0, 'int');

        if (!$id) {
            redirect('index.php?page=admin&action=pets', 'Pet não encontrado.', 'error');
        }

        $pet = $this->petModel->buscarPorId($id);

        if (!$pet) {
            redirect('index.php?page=admin&action=pets', 'Pet não encontrado.', 'error');
        }

        $erro = null;

        require __DIR__ . '/../views/admin/pet_form.php';
    }

    /**
     * Processa criação/edição de pet com upload de foto
     */
    public function petSalvar(): void
    {
        requireAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=pets');
        }

        // Coletar dados
        $id = sanitize($_POST['id'] ?? 0, 'int');
        $dados = [
            'nome' => sanitize($_POST['nome'] ?? '', 'string'),
            'idade' => sanitize($_POST['idade'] ?? '', 'string'),
            'porte' => in_array($_POST['porte'] ?? '', ['Pequeno', 'Médio', 'Grande'])
                ? $_POST['porte']
                : 'Médio',
            'sexo' => in_array($_POST['sexo'] ?? '', ['M', 'F'])
                ? $_POST['sexo']
                : 'M',
            'raca' => sanitize($_POST['raca'] ?? 'SRD', 'string'),
            'descricao' => sanitize($_POST['descricao'] ?? '', 'string'),
            'vacinado' => isset($_POST['vacinado']) ? 1 : 0,
            'castrado' => isset($_POST['castrado']) ? 1 : 0,
            'status' => in_array($_POST['status'] ?? '', ['disponivel', 'reservado', 'adotado'])
                ? $_POST['status']
                : 'disponivel'
        ];

        // Validações
        if (empty($dados['nome']) || empty($dados['idade']) || empty($dados['descricao'])) {
            $_SESSION['flash_message'] = 'Preencha todos os campos obrigatórios.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=' . ($id ? 'pet-editar&id=' . $id : 'pet-novo'));
            return;
        }

        // Processar upload de foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = $_FILES['foto'];
            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

            if (!in_array($extensao, $extensoesPermitidas)) {
                $_SESSION['flash_message'] = 'Formato de imagem não permitido. Use JPG, PNG ou WEBP.';
                $_SESSION['flash_type'] = 'error';
                redirect('index.php?page=admin&action=' . ($id ? 'pet-editar&id=' . $id : 'pet-novo'));
                return;
            }

            if ($foto['size'] > 5 * 1024 * 1024) { // 5MB
                $_SESSION['flash_message'] = 'A imagem deve ter no máximo 5MB.';
                $_SESSION['flash_type'] = 'error';
                redirect('index.php?page=admin&action=' . ($id ? 'pet-editar&id=' . $id : 'pet-novo'));
                return;
            }

            // Gerar nome único para a foto
            $nomeArquivo = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $dados['nome'])) . '_' . time() . '.' . $extensao;
            $caminhoDestino = __DIR__ . '/../assets/images/pets/' . $nomeArquivo;
            $caminhoRelativo = 'assets/images/pets/' . $nomeArquivo;

            if (move_uploaded_file($foto['tmp_name'], $caminhoDestino)) {
                // Se está editando e tinha foto anterior, excluir
                if ($id) {
                    $petAntigo = $this->petModel->buscarPorId($id);
                    if ($petAntigo && !empty($petAntigo['imagem'])) {
                        $fotoAntiga = __DIR__ . '/../' . $petAntigo['imagem'];
                        if (file_exists($fotoAntiga)) {
                            unlink($fotoAntiga);
                        }
                    }
                }
                $dados['imagem'] = $caminhoRelativo;
            }
        }

        // Salvar no banco
        try {
            if ($id > 0) {
                $this->petModel->atualizar($id, $dados);
                $message = 'Pet atualizado com sucesso!';
            } else {
                $this->petModel->criar($dados);
                $message = 'Pet cadastrado com sucesso!';
            }

            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = 'success';
            redirect('index.php?page=admin&action=pets');

        } catch (Exception $e) {
            error_log('Erro ao salvar pet: ' . $e->getMessage());
            $_SESSION['flash_message'] = 'Erro ao salvar pet.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=pets');
        }
    }

    /**
     * Exclui um pet
     */
    public function petExcluir(): void
    {
        requireAdminAuth();

        $id = sanitize($_GET['id'] ?? 0, 'int');

        if (!$id) {
            $_SESSION['flash_message'] = 'Pet não encontrado.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=pets');
            return;
        }

        try {
            // Buscar pet para excluir foto
            $pet = $this->petModel->buscarPorId($id);
            if ($pet && !empty($pet['imagem'])) {
                $caminhoFoto = __DIR__ . '/../' . $pet['imagem'];
                if (file_exists($caminhoFoto)) {
                    unlink($caminhoFoto);
                }
            }

            $this->petModel->excluir($id);
            $_SESSION['flash_message'] = 'Pet excluído com sucesso!';
            $_SESSION['flash_type'] = 'success';
        } catch (Exception $e) {
            error_log('Erro ao excluir pet: ' . $e->getMessage());
            $_SESSION['flash_message'] = 'Erro ao excluir pet.';
            $_SESSION['flash_type'] = 'error';
        }

        redirect('index.php?page=admin&action=pets');
    }

    // ============================================================
    // CRUD DE BLOG
    // ============================================================

    /**
     * Lista todos os posts do blog
     */
    public function blog(): void
    {
        requireAdminAuth();
        require __DIR__ . '/../views/admin/blog.php';
    }

    /**
     * Exibe formulário de criação/edição de post
     */
    public function blogForm(): void
    {
        requireAdminAuth();

        $id = sanitize($_GET['id'] ?? 0, 'int');
        $post = $id ? $this->blogModel->buscarPorId($id) : null;

        require __DIR__ . '/../views/admin/blog_form.php';
    }

    /**
     * Processa criação/edição de post
     */
    public function blogSalvar(): void
    {
        requireAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=blog');
        }

        $id = sanitize($_POST['id'] ?? 0, 'int');
        $dados = [
            'titulo' => sanitize($_POST['titulo'] ?? '', 'string'),
            'resumo' => sanitize($_POST['resumo'] ?? '', 'string'),
            'conteudo' => $_POST['conteudo'] ?? '',  // HTML permitido
            'imagem_destaque' => sanitize($_POST['imagem_destaque'] ?? '', 'string') ?: null,
            'meta_description' => sanitize($_POST['meta_description'] ?? '', 'string') ?: null,
            'ativo' => isset($_POST['ativo']) ? (int) $_POST['ativo'] : 1
        ];

        // Validações
        if (empty($dados['titulo']) || empty($dados['conteudo'])) {
            $_SESSION['flash_message'] = 'Título e conteúdo são obrigatórios.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=blog_form' . ($id ? '&id=' . $id : ''));
            return;
        }

        try {
            if ($id > 0) {
                $this->blogModel->atualizar($id, $dados);
                $message = 'Artigo atualizado com sucesso!';
            } else {
                $this->blogModel->criar($dados);
                $message = 'Artigo publicado com sucesso!';
            }

            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = 'success';
            redirect('index.php?page=admin&action=blog');

        } catch (Exception $e) {
            error_log('Erro ao salvar post: ' . $e->getMessage());
            $_SESSION['flash_message'] = 'Erro ao salvar artigo.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=blog');
        }
    }

    /**
     * Exclui um post
     */
    public function blogExcluir(): void
    {
        requireAdminAuth();

        $id = sanitize($_GET['id'] ?? 0, 'int');

        if (!$id) {
            $_SESSION['flash_message'] = 'Artigo não encontrado.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=blog');
            return;
        }

        try {
            $this->blogModel->excluir($id);
            $_SESSION['flash_message'] = 'Artigo excluído com sucesso!';
            $_SESSION['flash_type'] = 'success';
        } catch (Exception $e) {
            error_log('Erro ao excluir post: ' . $e->getMessage());
            $_SESSION['flash_message'] = 'Erro ao excluir artigo.';
            $_SESSION['flash_type'] = 'error';
        }

        redirect('index.php?page=admin&action=blog');
    }

    // ============================================================
    // CRUD DE GALERIA
    // ============================================================

    /**
     * Lista todas as fotos da galeria
     */
    public function gallery(): void
    {
        requireAdminAuth();
        require __DIR__ . '/../views/admin/gallery.php';
    }

    /**
     * Exibe formulário de upload de foto
     */
    public function galleryForm(): void
    {
        requireAdminAuth();

        $id = sanitize($_GET['id'] ?? 0, 'int');
        $foto = $id ? $this->galleryModel->buscarPorId($id) : null;

        require __DIR__ . '/../views/admin/gallery_form.php';
    }

    /**
     * Processa upload de foto
     */
    public function gallerySalvar(): void
    {
        requireAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=gallery');
        }

        $id = sanitize($_POST['id'] ?? 0, 'int');
        $tipo = sanitize($_POST['tipo'] ?? 'foto', 'string');

        $dados = [
            'titulo' => sanitize($_POST['titulo'] ?? '', 'string'),
            'descricao' => sanitize($_POST['descricao'] ?? '', 'string'),
            'tipo' => in_array($tipo, ['foto', 'video']) ? $tipo : 'foto',
            'ordem' => sanitize($_POST['ordem'] ?? 0, 'int'),
            'ativo' => isset($_POST['ativo']) ? 1 : 0
        ];

        // Validar título
        if (empty($dados['titulo'])) {
            $_SESSION['flash_message'] = 'Título é obrigatório.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=gallery_form' . ($id ? '&id=' . $id : ''));
            return;
        }

        // Processar baseado no tipo
        if ($dados['tipo'] === 'video') {
            // Processar URL de vídeo
            $videoUrl = sanitize($_POST['video_url'] ?? '', 'string');

            if (empty($videoUrl) && !$id) {
                $_SESSION['flash_message'] = 'URL do vídeo é obrigatória.';
                $_SESSION['flash_type'] = 'error';
                redirect('index.php?page=admin&action=gallery_form' . ($id ? '&id=' . $id : ''));
                return;
            }

            if (!empty($videoUrl)) {
                // Converter URL do YouTube para embed
                if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                    $dados['imagem'] = 'https://www.youtube.com/embed/' . $matches[1];
                }
                // Converter URL do Vimeo para embed
                elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $matches)) {
                    $dados['imagem'] = 'https://player.vimeo.com/video/' . $matches[1];
                }
                // URL já é embed ou formato não reconhecido
                else {
                    $dados['imagem'] = $videoUrl;
                }
            }
        } else {
            // Processar upload de foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto = $_FILES['foto'];
                $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

                if (!in_array($extensao, $extensoesPermitidas)) {
                    $_SESSION['flash_message'] = 'Formato de imagem não permitido.';
                    $_SESSION['flash_type'] = 'error';
                    redirect('index.php?page=admin&action=gallery_form' . ($id ? '&id=' . $id : ''));
                    return;
                }

                if ($foto['size'] > 5 * 1024 * 1024) {
                    $_SESSION['flash_message'] = 'A imagem deve ter no máximo 5MB.';
                    $_SESSION['flash_type'] = 'error';
                    redirect('index.php?page=admin&action=gallery_form' . ($id ? '&id=' . $id : ''));
                    return;
                }

                // Gerar nome único
                $nomeArquivo = 'gallery_' . time() . '_' . uniqid() . '.' . $extensao;
                $caminhoDestino = __DIR__ . '/../assets/images/gallery/' . $nomeArquivo;
                $caminhoRelativo = 'assets/images/gallery/' . $nomeArquivo;

                // Criar diretório se não existir
                $dir = __DIR__ . '/../assets/images/gallery';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                if (move_uploaded_file($foto['tmp_name'], $caminhoDestino)) {
                    // Se editando, excluir foto antiga
                    if ($id) {
                        $fotoAntiga = $this->galleryModel->buscarPorId($id);
                        if ($fotoAntiga && !empty($fotoAntiga['imagem'])) {
                            $caminhoAntigo = __DIR__ . '/../' . $fotoAntiga['imagem'];
                            if (file_exists($caminhoAntigo)) {
                                unlink($caminhoAntigo);
                            }
                        }
                    }
                    $dados['imagem'] = $caminhoRelativo;
                }
            } elseif (!$id && $dados['tipo'] === 'foto') {
                $_SESSION['flash_message'] = 'Selecione uma foto.';
                $_SESSION['flash_type'] = 'error';
                redirect('index.php?page=admin&action=gallery_form');
                return;
            }
        } // end if/else tipo

        try {
            if ($id > 0) {
                $this->galleryModel->atualizar($id, $dados);
                $message = 'Mídia atualizada com sucesso!';
            } else {
                $this->galleryModel->criar($dados);
                $message = 'Mídia adicionada com sucesso!';
            }

            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = 'success';
            redirect('index.php?page=admin&action=gallery');

        } catch (Exception $e) {
            error_log('Erro ao salvar foto: ' . $e->getMessage());
            $_SESSION['flash_message'] = 'Erro ao salvar foto.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=gallery');
        }
    }

    /**
     * Exclui uma foto
     */
    public function galleryExcluir(): void
    {
        requireAdminAuth();

        $id = sanitize($_GET['id'] ?? 0, 'int');

        if (!$id) {
            $_SESSION['flash_message'] = 'Foto não encontrada.';
            $_SESSION['flash_type'] = 'error';
            redirect('index.php?page=admin&action=gallery');
            return;
        }

        try {
            // Excluir arquivo
            $foto = $this->galleryModel->buscarPorId($id);
            if ($foto && !empty($foto['imagem'])) {
                $caminho = __DIR__ . '/../' . $foto['imagem'];
                if (file_exists($caminho)) {
                    unlink($caminho);
                }
            }

            $this->galleryModel->excluir($id);
            $_SESSION['flash_message'] = 'Foto excluída com sucesso!';
            $_SESSION['flash_type'] = 'success';
        } catch (Exception $e) {
            error_log('Erro ao excluir foto: ' . $e->getMessage());
            $_SESSION['flash_message'] = 'Erro ao excluir foto.';
            $_SESSION['flash_type'] = 'error';
        }

        redirect('index.php?page=admin&action=gallery');
    }

    // ============================================================
    // CONFIGURAÇÕES DO SITE
    // ============================================================

    /**
     * GET — Exibe a tela de configurações gerais
     */
    public function configuracoes(): void
    {
        requireAdminAuth();

        $appConfig  = require APP_ROOT . '/config/app.php';
        $successMsg = $_SESSION['cfg_success'] ?? '';
        $errorMsg   = $_SESSION['cfg_error']   ?? '';
        unset($_SESSION['cfg_success'], $_SESSION['cfg_error']);

        require APP_ROOT . '/views/admin/configuracoes.php';
    }

    /**
     * POST — Salva toggle + upload do QR Code
     */
    public function configuracoesSalvar(): void
    {
        requireAdminAuth();

        // ── Validação CSRF ──────────────────────────────────────
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['cfg_error'] = 'Token de segurança inválido. Tente novamente.';
            redirect('index.php?page=admin&action=configuracoes');
            return;
        }

        // ── Toggle doacao_ativa ──────────────────────────────────
        $doacaoAtiva = isset($_POST['doacao_ativa']) ? true : false;

        // ── Upload do QR Code ────────────────────────────────────
        $appConfigPath = APP_ROOT . '/config/app.php';
        $uploadErro    = '';

        if (!empty($_FILES['qrcode_doacao']['name'])) {
            $file     = $_FILES['qrcode_doacao'];
            $maxBytes = 2 * 1024 * 1024; // 2 MB

            // Validações de segurança
            $tiposPermitidos = ['image/png', 'image/jpeg', 'image/webp'];
            $mime = mime_content_type($file['tmp_name']);

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $uploadErro = 'Erro no upload do arquivo (código ' . $file['error'] . ').';

            } elseif (!in_array($mime, $tiposPermitidos, true)) {
                $uploadErro = 'Tipo de arquivo inválido. Use PNG, JPG ou WEBP.';

            } elseif ($file['size'] > $maxBytes) {
                $uploadErro = 'Arquivo muito grande. Máximo: 2 MB.';

            } else {
                // Destino: sempre salva como qrcode-doacao.png (substituindo anterior)
                $destDir  = APP_ROOT . '/assets/images/';
                $destFile = $destDir . 'qrcode-doacao.png';

                // Cria a pasta se não existir
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }

                // Converte para PNG se for JPG/WEBP, para garantir nome consistente
                if ($mime === 'image/png') {
                    move_uploaded_file($file['tmp_name'], $destFile);
                } else {
                    // Recodifica para PNG via GD
                    $img = ($mime === 'image/webp')
                        ? imagecreatefromwebp($file['tmp_name'])
                        : imagecreatefromjpeg($file['tmp_name']);

                    if ($img) {
                        imagepng($img, $destFile);
                        imagedestroy($img);
                    } else {
                        $uploadErro = 'Não foi possível processar a imagem.';
                    }
                }
            }
        }

        // ── Persistir doacao_ativa no app.php ───────────────────
        // Lê o arquivo, substitui apenas a chave e valida antes de gravar.
        // NUNCA grava se o conteúdo resultante for inválido.
        try {
            $conteudo = file_get_contents($appConfigPath);
            if ($conteudo === false) {
                throw new RuntimeException('Não foi possível ler o arquivo de configuração.');
            }

            $valor    = $doacaoAtiva ? 'true' : 'false';
            $novoConteudo = preg_replace(
                "/('doacao_ativa'\s*=>\s*)(true|false)/",
                "'doacao_ativa' => " . $valor,
                $conteudo
            );

            // Valida se o resultado ainda é PHP válido antes de gravar.
            // Nota: usa parênteses explícitos para garantir precedência correta.
            if ($novoConteudo === null || ($novoConteudo === $conteudo && $doacaoAtiva !== (bool)($appConfig['doacao_ativa'] ?? true))) {
                // preg_replace não encontrou o padrão ou falhou
                throw new RuntimeException('Padrão não encontrado no arquivo de configuração.');
            }

            // Persiste email_apoio: substitui o valor existente ou insere como string
            $emailApoioEscaped = addslashes($emailApoio);
            $novoConteudo = preg_replace(
                "/('email_apoio'\s*=>\s*)'[^']*'/",
                "'email_apoio' => '" . $emailApoioEscaped . "'",
                $novoConteudo
            );
            // Se a chave não existir no arquivo (primeira execução), não falha — será ignorada com segurança.

            // Verifica sintaxe básica: deve conter "return [" e fechar "];"
            if (!str_contains($novoConteudo, 'return [') || !str_contains($novoConteudo, '];')) {
                throw new RuntimeException('Resultado inválido — abortando gravação.');
            }

            $gravado = file_put_contents($appConfigPath, $novoConteudo, LOCK_EX);
            if ($gravado === false) {
                throw new RuntimeException('Sem permissão para gravar o arquivo de configuração (verifique CHMOD 644).');
            }
        } catch (RuntimeException $e) {
            error_log('[Configurações] Erro ao salvar app.php: ' . $e->getMessage());
            if (!$uploadErro) {
                $uploadErro = 'Não foi possível salvar a configuração: ' . $e->getMessage();
            }
        }


        // ── Feedback ─────────────────────────────────────────────
        if ($uploadErro) {
            $_SESSION['cfg_error'] = $uploadErro;
        } else {
            $_SESSION['cfg_success'] = 'Configurações salvas com sucesso!'
                . (!empty($_FILES['qrcode_doacao']['name']) ? ' QR Code atualizado.' : '');
        }

        redirect('index.php?page=admin&action=configuracoes');
    }
}
