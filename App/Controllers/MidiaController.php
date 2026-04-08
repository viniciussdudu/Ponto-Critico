<?php
namespace App\Controllers;

use App\Models\MidiaModel;

class MidiaController {
    private $model;

    public function __construct() {
        $this->model = new MidiaModel();
    }

    public function criar() {
        require_once __DIR__ . '/../Views/cadastro_midia.php';
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $tipo = trim($_POST['tipo'] ?? '');
            $genero = trim($_POST['genero'] ?? '');
            $data_lancamento = trim($_POST['data_lancamento'] ?? '');
            $sinopse = trim($_POST['sinopse'] ?? '');

            // Validar campos obrigatórios
            if (empty($titulo) || empty($tipo) || empty($genero) || empty($data_lancamento) || empty($sinopse)) {
                $erro = "Todos os campos são obrigatórios!";
                require_once __DIR__ . '/../Views/cadastro_midia.php';
                return;
            }

            // Criar novo ID único
            $novoId = 'mid_' . bin2hex(random_bytes(7));

            // Dados da nova mídia
            $novosDados = [
                'id' => $novoId,
                'titulo' => $titulo,
                'tipo' => $tipo,
                'genero' => $genero,
                'data_lancamento' => $data_lancamento,
                'sinopse' => $sinopse
            ];

            // Obter midias existentes
            $midias = $this->model->obterMidias();
            if (!is_array($midias)) {
                $midias = [];
            }

            // Adicionar nova mídia
            $midias[] = $novosDados;

            // Salvar no arquivo
            if ($this->model->atualizarMidias($midias)) {
                header('Location: index.php?url=home&sucesso=1');
                exit;
            } else {
                $erro = "Erro ao salvar a mídia!";
                require_once __DIR__ . '/../Views/cadastro_midia.php';
            }
        }
    }

    public function obterMidias() {
        return $this->model->obterMidias();
    }
}  
