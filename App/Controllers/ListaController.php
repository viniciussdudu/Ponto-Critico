<?php

namespace App\Controllers;

use App\Models\ListaModel;
use App\Models\MidiaModel;

class ListaController {

    // 1. ROTA: /lista/gerenciar
    // Exibe a tela com o botão de criar e a lista de pastas existentes
    public function gerenciar() {
        // Garante que o usuário está logado pegando o ID da sessão
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?url=login");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $listaModel = new ListaModel();

        // Busca apenas as listas criadas por esse usuário específico
        $listas = $listaModel->buscarListasPorUsuario($usuarioId);

        // Carrega a View da Etapa 2 passando os dados
        require_once __DIR__ . '/../Views/lista/gerenciar.php';
    }

    // 2. ROTA: /lista/criar (Método POST)
    // Processa o formulário de criação de uma nova lista
    public function criar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome'])) {
            $nomeLista = trim($_POST['nome']);
            $usuarioId = $_SESSION['usuario_id'];

            $listaModel = new ListaModel();
            $listaModel->criarNovaLista($nomeLista, $usuarioId);

            // Após criar, joga o usuário de volta para a tela de gerenciamento
            header("Location: index.php?url=lista/gerenciar");
            exit;
        }
    }

    // 3. ROTA: /lista/ver?id=X
    // Abre a lista específica, mostra as mídias dela e o campo de busca para adicionar novas
    public function ver() {
        if (!isset($_GET['id'])) {
            header("Location: index.php?url=lista/gerenciar");
            exit;
        }

        $listaId = (int)$_GET['id'];
        $listaModel = new ListaModel();
        $midiaModel = new MidiaModel(); // Model que gerencia seu catálogo geral

        // Busca os dados da lista (para saber o nome dela)
        $lista = $listaModel->buscarPorId($listaId);

        // Segurança: Se a lista não existir ou não for do usuário logado, barra o acesso
        if (!$lista || $lista['usuario_id'] != $_SESSION['usuario_id']) {
            header("Location: /lista/gerenciar");
            exit;
        }

        // Busca as mídias que JÁ ESTÃO salvas dentro dessa lista específica
        $midiasDaLista = $listaModel->buscarMidiasDaLista($listaId);

        // Busca TODAS as mídias cadastradas no sistema para preencher o <select> de busca
        $todasAsMidias = $midiaModel->obterMidias();

        // Carrega a View da Etapa 3
        // Antes estava: require_once "views/lista/ver.php";
    require_once __DIR__ . '/../Views/lista/ver.php';
    }

    // 4. ROTA: /lista/adicionar-midia (Método POST)
    // Processa o vínculo da mídia selecionada com a lista atual
    public function adicionarMidia() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $listaId = (int)$_POST['lista_id'];
            $midiaId = $_POST['midia_id'];


            $listaModel = new ListaModel();

            // Evita duplicar a mesma mídia na mesma lista
            if (!$listaModel->midiaJaExisteNaLista($listaId, $midiaId)) {
                $listaModel->adicionarMidiaNaLista($listaId, $midiaId);
            }

            // Redireciona de volta para a página da própria lista
            header("Location: index.php?url=lista/ver&id=" . $listaId);
            exit;
        }
    }
}