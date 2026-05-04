<?php

namespace App\Controllers;

// Importa os Models usados por este Controller
use App\Models\AvaliacaoModel;
use App\Models\MidiaModel;

class AvaliacaoController
{
    // ===================== Exibir formulário de nova avaliação
    public function criar(): void
    {
        // Instancia o Model de Mídias
        $midiaModel = new MidiaModel();

        // Busca todas as mídias cadastradas para preencher o select da view
        $midias = $midiaModel->obterMidias();

        // Carrega a tela de cadastro de avaliação
        require_once __DIR__ . '/../Views/avaliacao.php';
    }

    // ===================== Salvar nova avaliação
    public function salvar(): void
    {
        // Garante que a rota só aceite envio via POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }

        // Captura os dados enviados pelo formulário
        // midia_id é string porque o ID da mídia pode ser algo como "mid_abc123"
        $midiaId = $_POST['midia_id'] ?? '';

        // Usa float para aceitar meia estrela, exemplo: 0.5, 1.5, 4.5
        $nota = isset($_POST['nota']) ? (float) $_POST['nota'] : 0;

        // Remove espaços extras do comentário
        $comentario = trim($_POST['comentario'] ?? '');

        // Valida os campos obrigatórios
        // A nota precisa estar entre 0.5 e 5
        if (empty($midiaId) || $nota < 0.5 || $nota > 5 || empty($comentario)) {
            header('Location: index.php?url=avaliacao/criar&erro=campos_invalidos');
            exit;
        }

        // Instancia o Model de Avaliações
        $avaliacaoModel = new AvaliacaoModel();

        // Monta o array com os dados da nova avaliação
        $novaAvaliacao = [
            'id' => time(), // Gera um ID simples usando timestamp
            'midia_id' => $midiaId,
            'usuario_id' => $_SESSION['usuario_id'] ?? 0, // Usa usuário logado, se existir
            'nota' => $nota,
            'comentario' => $comentario,
            'data' => date('d/m/Y H:i')
        ];

        // Salva a avaliação no JSON
        if ($avaliacaoModel->salvar($novaAvaliacao)) {
            header('Location: index.php?url=home&sucesso=avaliacao');
            exit;
        }

        // Caso ocorra erro ao salvar
        echo "Erro crítico ao salvar no JSON.";
        exit;
    }

    // ===================== Exibir formulário de edição de avaliação
    public function editar(): void
    {
        // Captura o ID da avaliação pela URL
        $id = $_GET['id'] ?? null;

        // Se não houver ID, volta para a home
        if (!$id) {
            header('Location: index.php?url=home');
            exit;
        }

        // Busca a avaliação pelo ID
        $model = new AvaliacaoModel();
        $avaliacao = $model->obterPorId($id);

        // Se encontrar a avaliação, carrega a tela de edição
        if ($avaliacao) {
            require_once __DIR__ . '/../Views/editar_avaliacao.php';
            return;
        }

        // Se não encontrar, volta para a home
        header('Location: index.php?url=home');
        exit;
    }

    // ===================== Atualizar avaliação existente
    public function atualizar(): void
    {
        // Garante que a atualização só ocorra via POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }

        // Captura os dados enviados pelo formulário de edição
        $id = $_POST['id'] ?? null;

        // Usa float para permitir notas com meia estrela
        $nota = isset($_POST['nota']) ? (float) $_POST['nota'] : 0;

        // Limpa espaços extras do comentário
        $comentario = trim($_POST['comentario'] ?? '');

        // Valida se os dados estão corretos
        if (!$id || $nota < 0.5 || $nota > 5 || empty($comentario)) {
            header('Location: index.php?url=home&erro=avaliacao_invalida');
            exit;
        }

        // Instancia o Model e atualiza a avaliação no JSON
        $model = new AvaliacaoModel();
        $model->atualizar($id, $nota, $comentario);

        // Redireciona para a home após atualizar
        header('Location: index.php?url=home&sucesso=avaliacao_atualizada');
        exit;
    }
}