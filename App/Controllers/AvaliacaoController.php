<?php

namespace App\Controllers;

use App\Models\Avaliacao;
use App\Models\Midia;

class AvaliacaoController
{
    public function criar(): void
    {
        $midiaModel = new Midia();
        $midias = $midiaModel->listar();

        require_once __DIR__ . '/../Views/nova-avaliacao.php';
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }

        $midiaId = isset($_POST['midia_id']) ? (int) $_POST['midia_id'] : 0;
        $nota = isset($_POST['nota']) ? (int) $_POST['nota'] : 0;
        $comentario = trim($_POST['comentario'] ?? '');

        if ($midiaId <= 0 || $nota < 1 || $nota > 5 || empty($comentario)) {
            echo "<h1>Erro ao salvar avaliação</h1>";
            echo "<p>Preencha todos os campos corretamente.</p>";
            echo "<a href='index.php?url=avaliacao/criar'>Voltar</a>";
            return;
        }

        $avaliacaoModel = new Avaliacao();

        $novaAvaliacao = [
            'id' => time(),
            'midia_id' => $midiaId,
            'nota' => $nota,
            'comentario' => $comentario,
            'data' => date('Y-m-d H:i:s')
        ];

        $avaliacaoModel->salvar($novaAvaliacao);

        header('Location: index.php?url=home');
        exit;
    }
}