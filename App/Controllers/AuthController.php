<?php

namespace App\Controllers;
use App\Models\Usuario;
use App\Services\EmailService;

class AuthController {

   public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $userModel = new Usuario();
        $usuario = $userModel->buscarPorEmail($email);

        // 1. Verifica se o usuário existe e a senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            
            // 2. VERIFICAÇÃO DO STATUS
            if (isset($usuario['status']) && $usuario['status'] == 1) {
                // Login com sucesso!
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                
                header('Location: index.php?url=home');
                exit();
            } else {
                // Usuário existe, mas não confirmou o e-mail
                $erro = "Sua conta ainda não foi ativada. Verifique seu e-mail.";
                require_once __DIR__ . '/../Views/login.php';
            }
            
        } else {
            $erro = "E-mail ou senha incorretos.";
            require_once __DIR__ . '/../Views/login.php';
        }
    }
}
    public function confirmar() {
        $token = $_GET['token'] ?? null;

        if ($token) {
            $userModel = new Usuario();
            $usuarios = $userModel->listarTodos(); 
            $sucesso = false;

            foreach ($usuarios as &$usuario) {
                if (isset($usuario['token']) && $usuario['token'] === $token) {
                    $usuario['status'] = 1;      
                    $usuario['token'] = null;    
                    $sucesso = true;
                    break;
                }
            }

            if ($sucesso) {
                if ($userModel->atualizarLista($usuarios)) {
                    header('Location: index.php?url=login&confirmado=1');
                    exit();
                }
            }
        }

        die("Erro: Link de confirmação inválido ou expirado.");
    }




    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $userModel = new Usuario();

            $token = bin2hex(random_bytes(16));

            $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Erro: O formato do e-mail é inválido!";
    exit;
        }

            $dadosParaSalvar = [
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha,
                'status' => 0,
                'token' => $token
            ];

            if ($userModel->salvar($dadosParaSalvar)) {
            // Monta o link para o e-mail
            $link = "http://localhost:8000/index.php?url=confirmar&token=" . $token;
            
            $assunto = "Ative sua conta - Ponto Crítico";
            $corpo = "<h2>Quase lá, $nome!</h2>
                      <p>Clique no link abaixo para confirmar seu e-mail e ativar sua conta:</p>
                      <a href='$link'>Ativar minha conta</a>";

            // Envia o e-mail usando o seu novo Service
            EmailService::enviar($email, $assunto, $corpo);

            // Redireciona para um aviso (Combine com Arthur/Gustavo para criar essa view)
            header('Location: index.php?url=aviso-confirmacao');
            } else {
                echo "Erro: E-mail já cadastrado!";
            }
        }
    }

    public function redefinirSenha() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $userModel = new Usuario();
        $usuarios = $userModel->listarTodos();
        $encontrado = false;

        foreach ($usuarios as &$usuario) {
            if ($usuario['email'] === $email) {
                // Gera o token de segurança
                $token = bin2hex(random_bytes(16));
                $usuario['recuperar_token'] = $token;
                $encontrado = true;
                break;
            }
        }

        if ($encontrado && $userModel->atualizarLista($usuarios)) {
            // Prepara o link com a porta 8000 que você está usando
            $link = "http://localhost:8000/index.php?url=redefinir&token=" . $token;
            
            $assunto = "Recuperação de Senha - Ponto Crítico";
            $corpo = "<h1>Recuperação de Senha</h1>
                      <p>Você solicitou a troca de senha. Clique no link abaixo para prosseguir:</p>
                      <a href='{$link}'>Redefinir Minha Senha</a>";

            // Envia o e-mail
            \App\Services\EmailService::enviar($email, $assunto, $corpo);
            
            header('Location: index.php?url=aviso-confirmacao');
            exit();
        } else {
            header('Location: index.php?url=recuperar-senha&erro=usuario_nao_encontrado');
            exit();
        }
    }
}

    public function confirmarRedefinicao() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['token']; // O token que veio do campo hidden
        $novaSenha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);

        $userModel = new Usuario();
        $usuarios = $userModel->listarTodos();
        $sucesso = false;

        foreach ($usuarios as &$usuario) {
            // Procura o usuário pelo token de recuperação
            if (isset($usuario['recuperar_token']) && $usuario['recuperar_token'] === $token) {
                $usuario['senha'] = $novaSenha;
                $usuario['recuperar_token'] = null; // Limpa o token por segurança
                $sucesso = true;
                break;
            }
        }

        if ($sucesso && $userModel->atualizarLista($usuarios)) {
            header('Location: index.php?url=login&sucesso=senha_alterada');
            exit();
        } else {
            echo "Erro: Link inválido ou expirado.";
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
