<?php

namespace App\Controllers;

use App\Models\AvaliacaoModel;
use App\Models\MidiaModel;

class AvaliacaoController
{
    public function __construct()
    {
        // Garante que a sessão está sempre ativa para capturar os IDs de usuário de forma segura
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ===================== Exibir formulário de nova avaliação
    public function criar(): void
    {
        $midiaModel = new MidiaModel();

        // Busca todas as mídias cadastradas no Postgres
        $midias = $midiaModel->obterMidias();

        require_once __DIR__ . '/../Views/avaliacao.php';
    }

    // ===================== Salvar nova avaliação
    public function salvar(): void
    {
        // 1. Garante que a sessão está ativa ANTES de qualquer verificação
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }

        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=login&erro=necessario_login');
            exit;
        }

        
        $midiaId    = $_POST['midia_id'] ?? '';
        $nota       = isset($_POST['nota']) ? (float) $_POST['nota'] : 0;
        $comentario = trim($_POST['comentario'] ?? '');

        // Validação de campos obrigatórios
        if (empty($midiaId) || $nota < 0.5 || $nota > 5 || empty($comentario)) {
            header('Location: index.php?url=avaliacao/criar&erro=campos_invalidos');
            exit;
        }

        $avaliacaoModel = new AvaliacaoModel();

        
        $novaAvaliacao = [
            'midia_id'   => $midiaId,
            'usuario_id' => $_SESSION['usuario_id'],
            'nota'       => $nota,
            'comentario' => $comentario
        ];

        try {
            // Executa a inserção
            if ($avaliacaoModel->salvar($novaAvaliacao)) {
                header('Location: index.php?url=home&sucesso=avaliacao');
                exit;
            }
        } catch (\PDOException $e) {
        
            if ($e->getCode() === '23505' || strpos($e->getMessage(), 'unica_avaliacao_por_usuario') !== false) { 
                header('Location: index.php?url=home&erro=ja_avaliado');
                exit;
            }
            
    
        }

        
        header('Location: index.php?url=home&erro=critico_banco');
        exit;
    }

    // ===================== Exibir formulário de edição de avaliação
    public function editar(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?url=home');
            exit;
        }

        $model = new AvaliacaoModel();
        $avaliacao = $model->obterPorId($id);

        if ($avaliacao) {
            require_once __DIR__ . '/../Views/editar_avaliacao.php';
            return;
        }

        header('Location: index.php?url=home');
        exit;
    }

    // ===================== Atualizar avaliação existente
    public function atualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $nota = isset($_POST['nota']) ? (float) $_POST['nota'] : 0;
        $comentario = trim($_POST['comentario'] ?? '');

        if (!$id || $nota < 0.5 || $nota > 5 || empty($comentario)) {
            header('Location: index.php?url=home&erro=avaliacao_invalida');
            exit;
        }

        $model = new AvaliacaoModel();
        $model->atualizar($id, $nota, $comentario);

        header('Location: index.php?url=home&sucesso=avaliacao_atualizada');
        exit;
    }

    // ===================== Ver avaliação (detalhes)
    public function ver(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?url=home');
            exit;
        }

        $model = new AvaliacaoModel();
        $avaliacao = $model->obterPorId($id);

        if (!$avaliacao) {
            header('Location: index.php?url=home');
            exit;
        }

        require_once __DIR__ . '/../Views/ver_avaliacao.php';
    }

    // ===================== Interações de Like/Dislike vinculadas ao ID da Mídia
    public function like(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $avaliacaoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            header('Location: index.php?url=login');
            exit;
        }

        if ($avaliacaoId > 0) {
            $model = new \App\Models\AvaliacaoModel();
            $model->adicionarLikeAvaliacao($avaliacaoId, $usuarioId);
        }

    
        header('Location: index.php?url=avaliacao/ver&id=' . urlencode($avaliacaoId));
        exit;
    }

    public function deslike(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $avaliacaoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            header('Location: index.php?url=login');
            exit;
        }

        if ($avaliacaoId > 0) {
            $model = new \App\Models\AvaliacaoModel();
            $model->adicionarDeslikeAvaliacao($avaliacaoId, $usuarioId);
        }

        header('Location: index.php?url=avaliacao/ver&id=' . urlencode($avaliacaoId));
        exit;
    }

    // ===================== Comentar em uma avaliação (Sincrono)
    public function comentar(): void
    {
        $id = $_POST['avaliacao_id'] ?? null;
        $texto = trim($_POST['comentario'] ?? '');

        if ($id && !empty($texto) && isset($_SESSION['usuario_id'])) {
            $model = new AvaliacaoModel();

            $comentario = [
                'usuario_id' => $_SESSION['usuario_id'],
                'conteudo'   => $texto
            ];

            $model->adicionarComentario($id, $comentario);
        }

        header('Location: index.php?url=avaliacao/ver&id=' . urlencode($id));
        exit;
    }

    // ===================== API: Listar Avaliações por Mídia
    public function listarPorMidia(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        $midiaId = $_GET['midia_id'] ?? '';
        if (empty($midiaId)) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Parâmetro midia_id é obrigatório.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $model = new AvaliacaoModel();
        $resultado = $model->obterAvaliacoesDaMidia($midiaId);

        // === AJUSTE DE COMPATIBILIDADE PARA O JAVASCRIPT ===
        if (is_array($resultado)) {
            foreach ($resultado as &$av) {
                if (!isset($av['comentario']) && isset($av['texto_avaliacao'])) {
                    $av['comentario'] = $av['texto_avaliacao']; 
                }
                if (!isset($av['data']) && isset($av['data_avaliacao'])) {
                    $av['data'] = $av['data_avaliacao'];
                }
            
                $av['comentarios'] = $av['comentarios'] ?? []; 
            }
        }
        // ====================================================

        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ===================== API: Salvar avaliação via AJAX
    public function apiSalvar(): void
    {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }

        
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=login'); 
            exit;
        }

        $midiaId    = $_POST['midia_id'] ?? '';
        $nota       = isset($_POST['nota']) ? (float) $_POST['nota'] : 0;
        $comentario = trim($_POST['comentario'] ?? '');

        
        if (empty($midiaId) || $nota < 0.5 || $nota > 5 || empty($comentario)) {
        
            header('Location: index.php?url=avaliacao&erro=dados_invalidos');
            exit;
        }

        $model = new AvaliacaoModel();
        
        $dadosSalvar = [
            'midia_id'   => $midiaId,
            'usuario_id' => $_SESSION['usuario_id'],
            'nota'       => $nota,
            'comentario' => $comentario
        ];

        try {
            if (!$model->salvar($dadosSalvar)) {
                header('Location: index.php?url=home&erro=falha_salvar');
                exit;
            }
        } catch (\PDOException $e) {
        
            if ($e->getCode() === '23505') {
                header('Location: index.php?url=home&erro=ja_avaliado');
                exit;
            }
            
            header('Location: index.php?url=home&erro=db');
            exit;
        }

        header('Location: index.php?url=home');
        exit; 
    }
    // ===================== API: Listar Comentários de uma Avaliação
    public function listarComentarios(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        $avaliacaoId = $_GET['avaliacao_id'] ?? '';
        if (empty($avaliacaoId)) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Parâmetro avaliacao_id é obrigatório.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $model = new AvaliacaoModel();
        
        
        $sql = "SELECT c.id_comentario, c.conteudo as texto, c.data_comentario as data, u.nome as usuario_nome, u.id_usuario as usuario_id 
                FROM comentario c 
                INNER JOIN usuario u ON c.id_usuario = u.id_usuario 
                WHERE c.id_avaliacao = :id_avaliacao 
                ORDER BY c.data_comentario ASC";
        
        $db = \App\Models\Database::conectar();
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_avaliacao' => $avaliacaoId]);
        $comentarios = $stmt->fetchAll();

        echo json_encode($comentarios, JSON_UNESCAPED_UNICODE);
        return;
    }

    // ===================== API: Comentar via AJAX
    public function apiComentar(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido. Use POST.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'É necessário estar logado para enviar comentários.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $avaliacaoId = $_POST['avaliacao_id'] ?? '';
        $texto = trim($_POST['comentario'] ?? '');

        if (empty($avaliacaoId) || empty($texto)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'avaliacao_id e comentario são obrigatórios.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $comentario = [
            'usuario_id' => $_SESSION['usuario_id'],
            'comentario' => $texto
        ];

        $model = new AvaliacaoModel();
        $ok = $model->adicionarComentario($avaliacaoId, $comentario);

        if (!$ok) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Falha ao salvar comentário no banco.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Comentário enviado com sucesso.'
        ], JSON_UNESCAPED_UNICODE);
        return;
    }
    public function apiExcluirRapida() {

    echo "<h1>[TESTE 1] Chegou no Controller!</h1>";
    echo "<pre>Dados enviados pelo formulário (POST):<br>";
    print_r($_POST);
    echo "</pre>";
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpa buffers de saída para evitar quebras de cabeçalho
        if (ob_get_length()) {
            ob_clean();
        }

        // 1. Defesa preventiva: Se o usuário não estiver logado, barra a ação
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=login');
            exit;
        }

        // 2. Bloqueia acessos que não sejam via formulário POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }

        // 3. Resgata o ID enviado pelo input hidden da View
        $idAvaliacao = $_POST['id'] ?? '';
        $idUsuarioLogado = $_SESSION['usuario_id'];
        $tipoUsuarioLogado = $_SESSION['usuario_tipo'] ?? 'user';

        if (empty($idAvaliacao)) {
            header('Location: index.php?url=home&erro=id_invalido');
            exit;
        }

        
        $avaliacaoModel = new \App\Models\AvaliacaoModel();

        
        $excluiu = $avaliacaoModel->excluirAvaliacaoPorId($idAvaliacao, $idUsuarioLogado, $tipoUsuarioLogado);

        if ($excluiu) {
            
            header('Location: index.php?url=home&sucesso=avaliacao_removida');
            exit;
        } else {
            
            header('Location: index.php?url=home&erro=sem_permissao');
            exit;
        }
    }
}
