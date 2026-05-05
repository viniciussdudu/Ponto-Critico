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
        if (!isset($_SESSION['usuario']) || !$this->usuarioModel->eAdmin($_SESSION['usuario'])) {
            // Se não for admin, redireciona para a home (ou outra página de sua escolha)
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
        if (!isset($_SESSION['usuario']) || !$this->usuarioModel->eAdmin($_SESSION['usuario'])) {
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
}
