<?php

namespace App\Controllers;

use App\Models\ListaModel;
use App\Models\MidiaModel;

class ListaController {

    public function __construct() {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    
    public function gerenciar() {
        header("Location: index.php?url=perfil&tab=listas");
        exit;
    }

    
    public function criar() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?url=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome'])) {
            $nomeLista = trim($_POST['nome']);
            $usuarioId = $_SESSION['usuario_id'];

            $listaModel = new ListaModel();
            
            $listaModel->criarNovaLista($nomeLista, $usuarioId);

            
            header("Location: index.php?url=perfil&tab=listas");
            exit;
        }
    }

    
    public function ver() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?url=login");
            exit;
        }

        if (!isset($_GET['id'])) {
            
            header("Location: index.php?url=perfil&tab=listas");
            exit;
        }

        $listaId = (int)$_GET['id'];
        $listaModel = new ListaModel();
        $midiaModel = new MidiaModel();

        
        $lista = $listaModel->buscarPorId($listaId);

        
        if (!$lista || $lista['usuario_id'] != $_SESSION['usuario_id']) {
            header("Location: index.php?url=perfil&tab=listas&erro=lista_privada");
            exit;
        }

        
        $midiasDaLista = $listaModel->buscarMidiasDaLista($listaId);

        
        $todasAsMidias = $midiaModel->obterMidias();

        
        require_once __DIR__ . '/../Views/lista/ver.php';
    }

    
    public function adicionarMidia() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?url=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $listaId = (int)$_POST['lista_id'];
            $midiaId = $_POST['midia_id']; 

            $listaModel = new ListaModel();

            
            if (!$listaModel->midiaJaExisteNaLista($listaId, $midiaId)) {
                
                $listaModel->adicionarMidiaNaLista($listaId, $midiaId);
            }

            
            header("Location: index.php?url=lista/ver&id=" . $listaId);
            exit;
        }
    }

    public function excluirLista() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=perfil&tab=listas');
            exit;
        }

        $listaId = $_POST['lista_id'] ?? '';
        $usuarioId = $_SESSION['usuario_id'];

        if (empty($listaId)) {
            header('Location: index.php?url=perfil&tab=listas&erro=id_invalido');
            exit;
        }

        
        $listaModel = new \App\Models\ListaModel();
        
        
        $deletou = $listaModel->deletarListaPorId($listaId, $usuarioId);

        if ($deletou) {
            header('Location: index.php?url=perfil&tab=listas&sucesso=lista_removida');
        } else {
            header('Location: index.php?url=perfil&tab=listas&erro=erro_exclusao');
        }
        exit;
    }
}