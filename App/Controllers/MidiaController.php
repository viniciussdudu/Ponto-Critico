<?php

namespace App\Controllers;

use App\Models\MidiaModel;
use App\Models\Usuario;

class MidiaController {
    private $model;
    private $usuarioModel;

    public function __construct() {
        $this->model = new MidiaModel();
        $this->usuarioModel = new Usuario();
    }

    public function criar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se o usuário está logado e se é admin
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header('Location: index.php?url=home&erro=acesso_negado');
            exit;
        }

        require_once __DIR__ . '/../Views/cadastro_midia.php';
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $tipo = trim($_POST['tipo'] ?? '');
            $genero = trim($_POST['genero'] ?? '');
            $sinopse = trim($_POST['sinopse'] ?? '');
            $data_lancamento = trim($_POST['data_lancamento'] ?? null);
            
            $capaMidia = null; 

            // 1. PROCESSA O UPLOAD DA IMAGEM
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                
                
                $pastaDestino = dirname(__DIR__, 2) . '/Public/uploads/capas_midias/';
                
                
                if (!is_dir($pastaDestino)) {
                    mkdir($pastaDestino, 0755, true);
                }

                
                $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                
                
                $capaMidia = md5(uniqid(rand(), true)) . '.' . $extensao;
                
                $caminhoCompleto = $pastaDestino . $capaMidia;

                
                if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
                    $capaMidia = null; 
                }
            }

            
            $midiaModel = new \App\Models\MidiaModel();
            
            if ($midiaModel->inserir($titulo, $tipo, $genero, $sinopse, $data_lancamento, $capaMidia)) {
                header('Location: index.php?url=home&sucesso=midia_cadastrada');
                exit();
            } else {
                echo "Erro ao salvar a mídia no banco de dados.";
            }
        }
    }

    public function obterMidias() {
        return $this->model->obterMidias();
    }

    public function visualizarDetalhes() {
        $midiaId = $_GET['id'] ?? null;

        if (!$midiaId) {
            header('Location: index.php?url=home');
            exit;
        }

        
        $midia = $this->model->buscarPorId($midiaId);

        if (!$midia) {
            header('Location: index.php?url=home&erro=midia_nao_encontrada');
            exit;
        }

        
        $avaliacaoModel = new \App\Models\AvaliacaoModel();
        $avaliacoes = $avaliacaoModel->obterAvaliacoesDaMidia($midiaId);
        $notaMedia = $avaliacaoModel->calcularNotaMedia($midiaId);

        require_once __DIR__ . '/../Views/detalhes_midia.php';
    }

    public function apiListarOuFiltrar() {
        $titulo = $_GET['titulo'] ?? '';
        $genero = $_GET['genero'] ?? '';

        
        $resultados = $this->model->buscarPorFiltros($titulo, $genero);

        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resultados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function apiExcluirRapida() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (ob_get_length()) {
            ob_clean();
        }

        
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Acesso negado. Apenas administradores podem excluir mídias.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(405);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Método não permitido. Use POST.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'ID da mídia não informado.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        
        $excluiu = $this->model->excluirPorId($id);

        if ($excluiu) {
            
            header('Location: index.php?url=home&sucesso=midia_excluida');
            exit;
        }

        
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Mídia não encontrada.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
