<?php

namespace App\Controllers;

// CORREÇÃO: Importando os nomes exatos das classes dos seus Models
use App\Models\AvaliacaoModel; 
use App\Models\MidiaModel; 

class AvaliacaoController
{
    public function criar(): void
    {
        // CORREÇÃO: Instanciando MidiaModel (o nome que está no seu arquivo)
        $midiaModel = new MidiaModel();
        
        // CORREÇÃO: Chamando o método correto 'obterMidias'
        $midias = $midiaModel->obterMidias(); 

        // Carrega a sua View
        require_once __DIR__ . '/../Views/avaliacao.php';
    }

    public function salvar(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?url=home');
        exit;
    }

    // 1. REMOVA o (int). Deixe como string para aceitar o "mid_..."
    $midiaId = isset($_POST['midia_id']) ? $_POST['midia_id'] : '';
    $nota    = isset($_POST['nota']) ? (int) $_POST['nota'] : 0;
    $comentario = trim($_POST['comentario'] ?? '');

    // 2. Ajuste a validação: verifique se o midiaId não está vazio
    if (empty($midiaId) || $nota < 1 || $nota > 5 || empty($comentario)) {
        header('Location: index.php?url=avaliar&erro=campos_invalidos');
        exit;
    }

        // Usando o nome correto aqui também
        $avaliacaoModel = new AvaliacaoModel();

        $novaAvaliacao = [
            'id'           => time(),
            'midia_id'     => $midiaId,
            'usuario_id'   => $_SESSION['usuario_id'] ?? 0,
            'nota'         => $nota,
            'comentario'   => $comentario,
            'data'         => date('d/m/Y H:i')
        ];

        if ($avaliacaoModel->salvar($novaAvaliacao)) {
            header('Location: index.php?url=home&sucesso=avaliacao');
        } else {
            echo "Erro crítico ao salvar no JSON.";
        }
        exit;
    }
public function editar() {
        $id = $_GET['id'] ?? null;
        
        // Chamamos o Model explicitamente aqui
        $model = new \App\Models\AvaliacaoModel(); 
        $avaliacao = $model->obterPorId($id);
        
        if ($avaliacao) {
            // Nota: Garanti que o App está com 'A' maiúsculo aqui também!
            require_once __DIR__ . '/../Views/editar_avaliacao.php';
        } else {
            header('Location: index.php?url=home');
        }
    }

    public function atualizar() {
        $id = $_POST['id'] ?? null;
        $nota = $_POST['nota'] ?? null;
        $comentario = $_POST['comentario'] ?? null;

        if ($id && $nota && $comentario) {
            // Chamamos o Model explicitamente aqui também
            $model = new \App\Models\AvaliacaoModel();
            $model->atualizar($id, $nota, $comentario);
            header('Location: index.php?url=home');
            exit;
        }
    }
}