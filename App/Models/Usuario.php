<?php

namespace App\Models;

use PDO;

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    // ===================== Salvar ou Atualizar 
    public function salvar($dados) {
        if (!isset($dados['id_usuario']) && !isset($dados['id'])) {
            
            $senhaHash = password_hash($dados['senha'] ?? $dados['password'] ?? '', PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO usuario (tipo_usuario, token, nome, email, bio, foto_perfil, senha) 
                    VALUES (:tipo_usuario, :token, :nome, :email, :bio, :foto_perfil, :senha)
                    RETURNING id_usuario";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'tipo_usuario' => $dados['tipo_usuario'] ?? $dados['tipo'] ?? 'user',
                'token'        => $dados['token'] ?? null,
                'nome'         => $dados['nome'] ?? $dados['Nome'] ?? '',
                'email'        => $dados['email'] ?? '',
                'bio'          => $dados['bio'] ?? null,
                'foto_perfil'  => $dados['foto_perfil'] ?? null,
                'senha'        => $senhaHash
            ]);

            $idGerado = $stmt->fetchColumn();
            return $idGerado ? true : false;

        } else {
            $id = $dados['id_usuario'] ?? $dados['id'];

            $sql = "UPDATE usuario SET 
                        tipo_usuario = :tipo_usuario,
                        token = :token,
                        nome = :nome,
                        email = :email,
                        bio = :bio,
                        foto_perfil = :foto_perfil
                    WHERE id_usuario = :id_usuario";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'tipo_usuario' => $dados['tipo_usuario'] ?? $dados['tipo'] ?? 'user',
                'token'        => $dados['token'] ?? null,
                'nome'         => $dados['nome'] ?? $dados['Nome'] ?? '',
                'email'        => $dados['email'] ?? '',
                'bio'          => $dados['bio'] ?? null,
                'foto_perfil'  => $dados['foto_perfil'] ?? null,
                'id_usuario'   => $id
            ]);
        }
    }

    //======================================================= ativar por token
    public function ativarContaPorToken($token): bool {
        try {
            $stmt = $this->db->prepare("SELECT id_usuario, status FROM usuario WHERE token = :token");
            $stmt->execute(['token' => $token]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                if ((int)$usuario['status'] === 1) {
                    return true;
                }

                $sql = "UPDATE usuario 
                        SET status = 1, token = NULL 
                        WHERE id_usuario = :id_usuario";
                
                $stmtUpdate = $this->db->prepare($sql);
                return $stmtUpdate->execute(['id_usuario' => $usuario['id_usuario']]);
            }

            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    // ====================================== Buscar por Email
    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) return null;

        $user['id'] = $user['id_usuario'];
        $user['tipo'] = $user['tipo_usuario'];
        
        return $user;
    }

    // ============================= Buscar por ID
    public function buscarPorId($id) {
        if (!is_numeric($id)) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE id_usuario = :id_usuario");
        $stmt->execute(['id_usuario' => (int)$id]);
        $user = $stmt->fetch();

        if (!$user) return null;

        $user['id'] = $user['id_usuario'];
        $user['tipo'] = $user['tipo_usuario'];

        return $user;
    }

    // ========================== Listar Todos
    public function listarTodos() {
        $stmt = $this->db->query("SELECT * FROM usuario ORDER BY nome ASC");
        $usuarios = $stmt->fetchAll();

        foreach ($usuarios as &$user) {
            $user['id'] = $user['id_usuario'];
            $user['tipo'] = $user['tipo_usuario'];
        }

        return $usuarios;
    }

    public function gravarTokenRecuperacao($email, $token): bool {
        try {
            $stmt = $this->db->prepare("SELECT id_usuario FROM usuario WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $sql = "UPDATE usuario SET token = :token WHERE id_usuario = :id_usuario";
                $stmtUpdate = $this->db->prepare($sql);
                return $stmtUpdate->execute([
                    'token'      => $token,
                    'id_usuario' => $usuario['id_usuario']
                ]);
            }
            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    // ========================== Atualizar Senha (Recuperação de senha)
    public function atualizarSenha($email, $novaSenha) {
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("UPDATE usuario SET senha = :senha, token = NULL WHERE email = :email");
        
        return $stmt->execute([
            'senha' => $senhaHash,
            'email' => $email
        ]);
    }

    // ========================== Verificar se é Admin
    public function eAdmin($usuario) {
        $tipo = $usuario['tipo_usuario'] ?? $usuario['tipo'] ?? 'user';
        return $tipo === 'admin';
    }

    public function atualizarSenhaPorToken($token, $novaSenhaHash): bool {
        try {
            $stmt = $this->db->prepare("SELECT id_usuario FROM usuario WHERE token = :token AND token IS NOT NULL");
            $stmt->execute(['token' => $token]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $sql = "UPDATE usuario 
                        SET senha = :senha, token = NULL 
                        WHERE id_usuario = :id_usuario";
                
                $stmtUpdate = $this->db->prepare($sql);
                return $stmtUpdate->execute([
                    'senha'      => $novaSenhaHash,
                    'id_usuario' => $usuario['id_usuario']
                ]);
            }
            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }
}