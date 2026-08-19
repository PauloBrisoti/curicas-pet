<?php
/**
 * ============================================================
 * CONTROLLER: ADOÇÃO DE PETS
 * ============================================================
 * 
 * Gerencia a página pública de pets disponíveis para adoção.
 * Carrega dados do banco de dados via Pet model.
 * ============================================================
 */

require_once __DIR__ . '/../models/Pet.php';

class AdocaoController
{
    private Pet $petModel;

    public function __construct()
    {
        $this->petModel = new Pet();
    }

    /**
     * Exibe a página de adoção com lista de pets disponíveis
     */
    public function index(): void
    {
        // Carregar apenas pets disponíveis
        $pets = $this->petModel->listarDisponiveis();

        require __DIR__ . '/../views/adocao.php';
    }
}
