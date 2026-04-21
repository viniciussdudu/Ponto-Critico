<?php

namespace App\Controllers;

class UsuarioController {

    public function __construct() {
        // Verifica se o usuário está logado antes de qualquer ação de perfil
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
    }

    public function exibirEdicao() {
    $usuarioModel = new \App\Models\Usuario();
    // Busca os dados do usuário logado usando o ID da sessão
    $usuario = $usuarioModel->buscarPorId($_SESSION['usuario_id']);
    
    // Passa a variável $usuario para a view
    require_once __DIR__ . '/../Views/editar_perfil.php';
}

    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }

        $caminhoUsuarios = __DIR__ . '/../../data/usuarios.json';
        $usuarios = json_decode(file_get_contents($caminhoUsuarios), true);

        // Atualiza os dados na memória
        foreach ($usuarios as &$usuario) {
            if ($usuario['id'] == $_SESSION['usuario_id']) {
                $usuario['nome'] = trim($_POST['nome']);
                $usuario['email'] = trim($_POST['email']);
                break;
            }
        }

        // Salva de volta no JSON
        if (file_put_contents($caminhoUsuarios, json_encode($usuarios, JSON_PRETTY_PRINT))) {
            $_SESSION['usuario_nome'] = $_POST['nome']; // Atualiza o nome na sessão
            header('Location: index.php?url=home&sucesso=perfil_atualizado');
        } else {
            echo "Erro ao atualizar perfil.";
        }
    }
}