<?php

namespace App\Controllers;

class UsuarioController {

    public function __construct() {
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
        
        $usuario = $usuarioModel->buscarPorId($_SESSION['usuario_id']);
        
        
        require_once __DIR__ . '/../Views/editar_perfil.php';
    }

    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=home');
            exit;
        }
        require_once __DIR__ . '/../Models/Usuario.php';

        
        $usuarioModel = new \App\Models\Usuario();
        $idUsuario = $_SESSION['usuario_id'];

        
        $usuarioAtual = $usuarioModel->buscarPorId($idUsuario);
        
        if (!$usuarioAtual) {
            echo "Usuário não encontrado.";
            exit;
        }

        
        $nomeFotoFinal = $usuarioAtual['foto_perfil'] ?? null;

        // 2. Processa o Upload da Nova Foto de Perfil (se o usuário enviou um arquivo)
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $arquivoTmp = $_FILES['foto_perfil']['tmp_name'];
            $nomeOriginal = $_FILES['foto_perfil']['name'];
            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($extensao, $extensoesPermitidas)) {
                $diretorioUpload = 'uploads/fotos_perfis/';
                
                
                if (!is_dir($diretorioUpload)) {
                    mkdir($diretorioUpload, 0755, true);
                }

                
                $novoNomeFoto = md5(uniqid(rand(), true)) . '.' . $extensao;
                $caminhoDestino = $diretorioUpload . $novoNomeFoto;

                
                if (move_uploaded_file($arquivoTmp, $caminhoDestino)) {
                    
                    
                    if (!empty($usuarioAtual['foto_perfil'])) {
                        $fotoAntiga = $diretorioUpload . $usuarioAtual['foto_perfil'];
                        if (file_exists($fotoAntiga)) {
                            unlink($fotoAntiga);
                        }
                    }
                    $nomeFotoFinal = $novoNomeFoto;
                }
            }
        }

        $dadosAtualizacao = [
            'id_usuario'   => $idUsuario,
            'nome'         => trim($_POST['nome']),
            'email'        => trim($_POST['email']),
            'bio'          => isset($_POST['bio']) ? trim($_POST['bio']) : null,
            'foto_perfil'  => $nomeFotoFinal,
            'tipo_usuario' => $usuarioAtual['tipo_usuario'] ?? 'user',
            'token'        => $usuarioAtual['token'] ?? null
        ];

        
        if ($usuarioModel->salvar($dadosAtualizacao)) {
            $_SESSION['usuario_nome'] = trim($_POST['nome']); 
            header('Location: index.php?url=perfil&sucesso=perfil_atualizado');
            exit;
        } else {
            echo "Erro ao atualizar perfil no banco de dados.";
        }
    }

    public function listarLogs() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
            http_response_code(403); 
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Acesso negado. Apenas administradores podem ver os logs.'
            ]);
            exit;
        }

        $caminhoLogs = __DIR__ . '/../../data/logs_acesso.json';
        
        $logs = [];
        if (file_exists($caminhoLogs)) {
            $logs = json_decode(file_get_contents($caminhoLogs), true) ?? [];
        }

        echo json_encode($logs);
        exit;
    }
}