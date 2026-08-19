<?php
/**
 * ============================================================
 * CONTROLLER: BLOG
 * ============================================================
 * 
 * Gerencia as páginas públicas do blog.
 * ============================================================
 */

require_once __DIR__ . '/../models/Blog.php';
require_once __DIR__ . '/../config/functions.php';

class BlogController
{
    private Blog $blogModel;

    public function __construct()
    {
        $this->blogModel = new Blog();
    }

    /**
     * Página de listagem do blog
     */
    public function index(): void
    {
        $posts = $this->blogModel->listarAtivos(20);
        $postsPopulares = $this->blogModel->maisLidos(5);

        require __DIR__ . '/../views/blog.php';
    }

    /**
     * Página de artigo individual
     */
    public function artigo(): void
    {
        $slug = $_GET['slug'] ?? '';

        if (empty($slug)) {
            header('Location: index.php?page=blog');
            exit;
        }

        $post = $this->blogModel->buscarPorSlug($slug);

        if (!$post) {
            // Redirecionar para 404 ou blog
            header('Location: index.php?page=blog');
            exit;
        }

        // Posts relacionados (outros posts)
        $postsRelacionados = $this->blogModel->listarAtivos(3);

        require __DIR__ . '/../views/blog_artigo.php';
    }
}
