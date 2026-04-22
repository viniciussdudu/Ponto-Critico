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
    public function editar() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo "ID da avaliação não fornecido.";
            return;
        }

        $avaliacao = $this->model->obterPorId($id);
        
        if (!$avaliacao) {
            echo "Avaliação não encontrada.";
            return;
        }

        // Carrega a View passando a variável $avaliacao
        require_once __DIR__ . '/../Views/editar_avaliacao.php';
    }

    // Recebe os dados do formulário e manda o Model salvar
    public function atualizar() {
        $id = $_POST['id'] ?? null;
        $nota = $_POST['nota'] ?? null;
        $comentario = $_POST['comentario'] ?? null;

        if ($id && $nota && $comentario) {
            $this->model->atualizar($id, $nota, $comentario);
            
            // Redireciona de volta para a página inicial após salvar
            header('Location: index.php?url=home');
            exit;
        } else {
            echo "Preencha todos os campos.";
        }
    }
}
