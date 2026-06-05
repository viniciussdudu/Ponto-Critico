<?php
namespace App\Controllers;

use App\Models\MidiaModel;
use App\Models\Usuario; // 1. Importando o model de Usuario

class MidiaController {
    private $model;
    private $usuarioModel; // 2. Adicionando a propriedade

    public function __construct() {
        $this->model = new MidiaModel();
        $this->usuarioModel = new Usuario(); // 3. Instanciando o model de Usuario
    }

    public function criar() {
        // 4. Inicia a sessão se já não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 5. Verifica se o usuário está logado e se é admin
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: index.php?url=home&erro=acesso_negado');
    exit;
}

        require_once __DIR__ . '/../Views/cadastro_midia.php';
    }

    public function salvar() {
        // Inicia a sessão se já não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Trava de segurança também no backend para impedir envios POST de usuários comuns
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: index.php?url=home&erro=acesso_negado');
    exit;
}

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
    public function apiListarOuFiltrar() {
        // Captura os termos de busca enviados na URL (ex: ?titulo=Vingadores&genero=Ação)
        $titulo = $_GET['titulo'] ?? '';
        $genero = $_GET['genero'] ?? '';

        // Obtém os dados filtrados usando o novo método do Model
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

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Acesso negado. Apenas administradores podem excluir mídias.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Método não permitido. Use POST.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'ID da mídia não informado.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $excluiu = $this->model->excluirPorId($id);

    if ($excluiu) {
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Mídia excluída com sucesso.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(404);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Mídia não encontrada.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

}
