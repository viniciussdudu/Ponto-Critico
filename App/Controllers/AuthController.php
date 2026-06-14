<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\AvaliacaoModel;
use App\Services\EmailService;

class AuthController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            $userModel = new Usuario();
            $usuario = $userModel->buscarPorEmail($email);

            // 1. Verifica se o usuário existe e se a senha criptografada bate
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                
                // 2. Verifica se a conta está ativada (status == 1)
                if (isset($usuario['status']) && (int)$usuario['status'] === 1) {
                    
                    // Define os dados da sessão com as chaves normatizadas do banco
                    $_SESSION['usuario_id']   = $usuario['id_usuario'] ?? $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'] ?? $usuario['tipo'] ?? 'comum';

                    // ==================== LOGS DE ACESSO (JSON) ====================
                    $caminhoLogs = __DIR__ . '/../../data/logs_acesso.json';
                    $logs = [];
                    
                    if (file_exists($caminhoLogs)) {
                        $logs = json_decode(file_get_contents($caminhoLogs), true) ?? [];
                    }

                    $novoLog = [
                        'usuario_email' => $usuario['email'],
                        'data_hora'     => date('Y-m-d H:i:s'),
                        'ip'            => $_SERVER['REMOTE_ADDR'] === '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'],
                        'evento'        => 'Login realizado com sucesso'
                    ];

                    array_unshift($logs, $novoLog);
                    $logs = array_slice($logs, 0, 50); // Mantém apenas os últimos 50 logs por performance

                    file_put_contents($caminhoLogs, json_encode($logs, JSON_PRETTY_PRINT));
                    // ===============================================================
                    
                    header('Location: index.php?url=home');
                    exit();

                } else {
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
            
            // O Model agora faz um UPDATE cirúrgico WHERE token = :token direto no PostgreSQL
            if ($userModel->ativarContaPorToken($token)) {
                header('Location: index.php?url=login&confirmado=1');
                exit();
            }
        }

        die("Erro: Link de confirmação inválido ou expirado.");
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome  = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Erro: O formato do e-mail é inválido!";
                exit;
            }

            $userModel = new Usuario();
            $token = bin2hex(random_bytes(16));

            $dadosParaSalvar = [
                'nome'   => $nome,
                'email'  => $email,
                'senha'  => $senha, 
                'status' => 0,
                'token'  => $token
            ];

            if ($userModel->salvar($dadosParaSalvar)) {
                $link = "http://127.0.0.1/pontocritico/index.php?url=confirmar&token=" . $token;
                
                $assunto = "Ative sua conta - Ponto Crítico";
                $corpo   = "<h2>Quase lá, $nome!</h2>
                            <p>Clique no link abaixo para confirmar seu e-mail e ativar sua conta:</p>
                            <a href='$link'>Ativar minha conta</a>";

                EmailService::enviar($email, $assunto, $corpo);

                header('Location: index.php?url=aviso-confirmacao');
                exit();
            } else {
                echo "Erro: E-mail já cadastrado!";
            }
        }
    }

    public function redefinirSenha() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $userModel = new Usuario();
            
            $token = bin2hex(random_bytes(16));

            // O Model grava o recuperar_token direto no registro do usuário via SQL
            if ($userModel->gravarTokenRecuperacao($email, $token)) {
                $link = "http://127.0.0.1/pontocritico/index.php?url=redefinir&token=" . $token;
                
                $assunto = "Recuperação de Senha - Ponto Crítico";
                $corpo   = "<h1>Recuperação de Senha</h1>
                            <p>Você solicitou a troca de senha. Clique no link abaixo para prosseguir:</p>
                            <a href='{$link}'>Redefinir Minha Senha</a>";

                EmailService::enviar($email, $assunto, $corpo);
                
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
            $token     = $_POST['token'] ?? '';
            $novaSenha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);

            $userModel = new Usuario();

            if ($userModel->atualizarSenhaPorToken($token, $novaSenha)) {
                header('Location: index.php?url=login&sucesso=senha_alterada');
                exit();
            } else {
                echo "Erro: Link inválido ou expirado.";
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?url=home');
        exit();
    }

    public function visualizarPerfil() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php?url=login');
        exit();
    }

    $usuarioModel = new Usuario();
    $avaliacaoModel = new AvaliacaoModel();
    
    
    $listaModel = new \App\Models\ListaModel(); 

    $notaFiltro = filter_input(INPUT_GET, 'nota', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5]
    ]);
    if ($notaFiltro === false) {
        $notaFiltro = null;
    }

    $dadosUsuario = $usuarioModel->buscarPorId($_SESSION['usuario_id']);
    
    $avaliacoesUsuario = $avaliacaoModel->obterAvaliacoesDoUsuario($_SESSION['usuario_id'], $notaFiltro);

    
    $listas = $listaModel->buscarListasPorUsuario($_SESSION['usuario_id']);

    if (!$dadosUsuario) {
        session_destroy();
        header('Location: index.php?url=login');
        exit();
    }

    
    require_once __DIR__ . '/../Views/perfil.php';
}
}