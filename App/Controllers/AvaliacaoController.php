<?php
namespace App\Controllers;

use App\Models\AvaliacaoModel;

class AvaliacaoController {
    private $model;

    public function __construct() {
        $this->model = new AvaliacaoModel();
    }

    public function listar() {
        $avaliacoes = $this->model->obterAvaliacoesComMidia();

        require_once __DIR__ . '/../Views/listar_avaliacoes.php';
    }
}