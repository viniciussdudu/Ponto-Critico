<?php

namespace App\Controllers;
use App\Models\Usuario;

class AuthController {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $userModel = new Usuario();
            $usuario = $userModel->buscarPorEmail($email);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Inicia a sessão se ainda não foi iniciada
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Salva os dados na sessão (importante para o index.php)
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                header('Location: index.php?url=home');
                exit();
            } else {
                header('Location: index.php?url=login&erro=1');
                exit();
            }
        }
    }


    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $userModel = new Usuario();

            $dadosParaSalvar = [
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha
            ];

            if ($userModel->salvar($dadosParaSalvar)) {
                header('Location: index.php?url=login&sucesso=1');
            } else {
                echo "Erro: E-mail já cadastrado!";
            }
        }
    }

    public function redefinirSenha() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $userModel = new Usuario();

            if ($userModel->buscarPorEmail($email)) {
                header("Location: index.php?url=redefinir&email=" . urlencode($email));
                exit();
            } else {
                header('Location: index.php?url=recuperar-senha&erro=usuario_nao_encontrado');
                exit();
            }
        }
    }

    public function confirmarRedefinicao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $novaSenha = $_POST['nova_senha'];

            $userModel = new Usuario();

            if ($userModel->atualizarSenha($email, $novaSenha)) {
                header('Location: index.php?url=login&sucesso=senha_alterada');
                exit();
            } else {
                echo "Erro ao atualizar a senha.";
            }
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php?url=home');
        exit();
    }


    public function visualizarPerfil() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=login');
            exit();
        }

        $usuarioModel = new Usuario();


        $dadosUsuario = $usuarioModel->buscarPorId($_SESSION['usuario_id']);

        if (!$dadosUsuario) {
            session_destroy();
            header('Location: index.php?url=login');
            exit();
        }

        require_once __DIR__ . '/../Views/perfil.php';
    }

}
